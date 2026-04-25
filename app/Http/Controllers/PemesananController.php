<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PemesananController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with(['menus' => function ($query) {
            $query->where('is_available', true)->orderBy('nama_menu');
        }])->orderBy('nama_vendor')->get();

        $vendorsData = $vendors->map(function ($vendor) {
            return [
                'idvendor' => $vendor->idvendor,
                'nama_vendor' => $vendor->nama_vendor,
                'menus' => $vendor->menus->map(function ($menu) {
                    return [
                        'idmenu' => $menu->idmenu,
                        'nama_menu' => $menu->nama_menu,
                        'harga' => (int) $menu->harga,
                        'path_gambar' => $menu->path_gambar,
                    ];
                })->values(),
            ];
        })->values();

        return view('pages.kantin.order', compact('vendorsData'));
    }

    public function menusByVendor(Vendor $vendor): JsonResponse
    {
        $menus = $vendor->menus()
            ->where('is_available', true)
            ->orderBy('nama_menu')
            ->get(['idmenu', 'nama_menu', 'harga', 'path_gambar']);

        return response()->json([
            'vendor' => [
                'idvendor' => $vendor->idvendor,
                'nama_vendor' => $vendor->nama_vendor,
            ],
            'menus' => $menus,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendor,idvendor',
            'payment_method' => 'required|in:virtual_account,qris,midtrans',
            'items' => 'required|array|min:1',
            'items.*.idmenu' => 'required|exists:menu,idmenu',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        $paymentMethod = $validated['payment_method'];
        $pesanan = null;

        $pesanan = DB::transaction(function () use ($validated, $paymentMethod) {
            $guest = $this->createGuestUser();
            $vendor = Vendor::findOrFail($validated['vendor_id']);

            $pesanan = Pesanan::create([
                'order_code' => 'TMP-' . Str::upper(Str::random(12)),
                'user_id' => $guest->id,
                'idvendor' => $vendor->idvendor,
                'nama' => $guest->name,
                'timestamp' => now(),
                'total' => 0,
                'metode_bayar' => $paymentMethod,
                'status_bayar' => 0,
                'midtrans_status' => $paymentMethod === 'midtrans' ? 'pending' : null,
                'payment_reference' => null,
                'gateway_payload' => [
                    'provider' => $paymentMethod === 'midtrans' ? 'midtrans' : 'simulator',
                    'method' => $paymentMethod,
                    'status' => 'pending',
                ],
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $menu = Menu::where('idmenu', $item['idmenu'])
                    ->where('idvendor', $vendor->idvendor)
                    ->firstOrFail();

                $jumlah = (int) $item['jumlah'];
                $harga = (int) $menu->harga;
                $subtotal = $harga * $jumlah;
                $total += $subtotal;

                DetailPesanan::create([
                    'idmenu' => $menu->idmenu,
                    'idpesanan' => $pesanan->idpesanan,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'timestamp' => now(),
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            $orderCode = 'PSN-' . str_pad((string) $pesanan->idpesanan, 7, '0', STR_PAD_LEFT);
            $paymentReference = strtoupper(substr($paymentMethod, 0, 3)) . '-' . $orderCode;

            $pesanan->update([
                'order_code' => $orderCode,
                'total' => $total,
                'payment_reference' => $paymentReference,
                'gateway_payload' => [
                    'provider' => $paymentMethod === 'midtrans' ? 'midtrans' : 'simulator',
                    'method' => $paymentMethod,
                    'reference' => $paymentReference,
                    'amount' => $total,
                    'status' => 'pending',
                ],
            ]);
            return $pesanan->fresh();
        });

        abort_unless($pesanan instanceof Pesanan, 500, 'Gagal membuat pesanan.');

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat. Lanjutkan pembayaran sekarang.',
            'payment_url' => route('kantin.payment.show', $pesanan->order_code),
            'order_code' => $pesanan->order_code,
        ]);
    }

    public function payment(Pesanan $pesanan)
    {
        $pesanan->load(['details.menu', 'vendor', 'user']);

        $snapToken = null;
        $paymentMethod = $pesanan->metode_bayar;
        $qrCodeSvg = null;

        if ((int) $pesanan->status_bayar === 1) {
            $qrCodeSvg = QrCode::format('svg')
                ->size(180)
                ->margin(1)
                ->generate((string) $pesanan->idpesanan);
        }

        if ($paymentMethod === 'midtrans' && config('midtrans.server_key')) {
            try {
                $midtrans = new MidtransService();
                $snapToken = $midtrans->generateSnapToken($pesanan);
            } catch (\Exception $e) {
                Log::error('Midtrans token generation failed: ' . $e->getMessage());
                $snapToken = null;
            }
        }
        return view('pages.kantin.payment', compact('pesanan', 'snapToken', 'paymentMethod', 'qrCodeSvg'));
    }

    public function confirmPayment(Request $request, Pesanan $pesanan)
    {
        if ((int) $pesanan->status_bayar === 1) {
            return redirect()
                ->route('kantin.payment.show', $pesanan->order_code)
                ->with('success', 'Pesanan ini sudah berstatus Lunas.');
        }

        if ($pesanan->metode_bayar === 'midtrans') {
            $midtrans = new MidtransService();
            $status = $midtrans->checkStatus($pesanan->order_code);

            if ($status['success'] && $status['status'] === 'settlement') {
                $pesanan->update([
                    'status_bayar' => 1,
                    'paid_at' => now(),
                    'midtrans_status' => 'settlement',
                    'gateway_payload' => array_merge($pesanan->gateway_payload ?? [], [
                        'status' => 'paid',
                        'midtrans_status' => 'settlement',
                        'confirmed_at' => now()->toDateTimeString(),
                    ]),
                ]);

                return redirect()
                    ->route('kantin.payment.show', $pesanan->order_code)
                    ->with('success', 'Pembayaran berhasil diverifikasi dari Midtrans. Status pesanan menjadi Lunas.');
            } else {
                return redirect()
                    ->route('kantin.payment.show', $pesanan->order_code)
                    ->with('error', 'Pembayaran belum terverifikasi oleh Midtrans.');
            }
        }

        $pesanan->update([
            'status_bayar' => 1,
            'paid_at' => now(),
            'gateway_payload' => array_merge($pesanan->gateway_payload ?? [], [
                'status' => 'paid',
                'confirmed_at' => now()->toDateTimeString(),
            ]),
        ]);

        return redirect()
            ->route('kantin.payment.show', $pesanan->order_code)
            ->with('success', 'Pembayaran berhasil divalidasi dan status berubah menjadi Lunas.');
    }

    public function webhookMidtrans(Request $request)
    {
        $data = $request->all();
        $midtrans = new MidtransService();
        $result = $midtrans->handleCallback($data);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    protected function createGuestUser(): User
    {
        $guest = User::create([
            'name' => 'Guest',
            'email' => 'guest-' . Str::uuid() . '@kantin.local',
            'password' => bcrypt(Str::random(32)),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $guest->update([
            'name' => 'Guest_' . str_pad((string) $guest->id, 7, '0', STR_PAD_LEFT),
            'email' => 'guest_' . str_pad((string) $guest->id, 7, '0', STR_PAD_LEFT) . '@kantin.local',
        ]);

        return $guest->fresh();
    }
}