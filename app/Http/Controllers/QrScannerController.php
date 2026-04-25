<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrScannerController extends Controller
{
    public function index()
    {
        return view('pages.qr-scanner');
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payload' => 'required|string|max:50',
        ]);

        $payload = trim($validated['payload']);

        if (! ctype_digit($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'QR code tidak valid. Payload harus berisi ID pesanan.',
            ], 422);
        }

        $pesanan = Pesanan::with(['vendor', 'details.menu', 'user'])->find((int) $payload);

        if (! $pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan ditemukan.',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'order_code' => $pesanan->order_code,
                'nama' => $pesanan->nama,
                'vendor' => $pesanan->vendor?->nama_vendor,
                'total' => (int) $pesanan->total,
                'metode_bayar' => $pesanan->metode_bayar,
                'status_bayar' => (int) $pesanan->status_bayar,
                'paid_at' => optional($pesanan->paid_at)->format('d M Y H:i'),
                'payment_reference' => $pesanan->payment_reference,
                'items' => $pesanan->details->map(function ($detail) {
                    return [
                        'nama_menu' => $detail->menu?->nama_menu,
                        'jumlah' => (int) $detail->jumlah,
                        'subtotal' => (int) $detail->subtotal,
                    ];
                })->values(),
            ],
        ]);
    }
}