<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorKantinController extends Controller
{
    public function adminKantin(Request $request, Vendor $vendor)
    {
        $menus = $vendor->menus()->orderByDesc('idmenu')->get();

        $ordersQuery = Pesanan::with(['details.menu', 'user'])
            ->where('idvendor', $vendor->idvendor)
            ->orderByDesc('timestamp');

        $pendingOrders = (clone $ordersQuery)->where('status_bayar', 0)->get();
        $paidOrders = (clone $ordersQuery)->where('status_bayar', 1)->get();

        $editMenu = null;
        if ($request->filled('menu')) {
            $editMenu = $vendor->menus()->where('idmenu', $request->integer('menu'))->first();
        }

        $summary = [
            'total_menu' => $menus->count(),
            'total_order_pending' => $pendingOrders->count(),
            'total_order_lunas' => $paidOrders->count(),
            'total_pendapatan' => $paidOrders->sum('total'),
        ];

        return view('pages.admin.vendor.kelola', compact('vendor', 'menus', 'pendingOrders', 'paidOrders', 'editMenu', 'summary'));
    }

    public function adminStoreMenu(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'path_gambar' => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
        ]);

        $this->syncBarangFromVendorInput($validated['nama_menu'], (int) $validated['harga']);

        $vendor->menus()->create([
            'nama_menu' => $validated['nama_menu'],
            'harga' => $validated['harga'],
            'path_gambar' => $validated['path_gambar'] ?? null,
            'is_available' => $request->boolean('is_available', true),
        ]);

        return redirect()->route('admin.vendor.kelola', $vendor->idvendor)->with('success', 'Menu berhasil ditambahkan.');
    }

    public function adminUpdateMenu(Request $request, Vendor $vendor, Menu $menu)
    {
        abort_unless((int) $menu->idvendor === (int) $vendor->idvendor, 404);

        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'path_gambar' => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
        ]);

        $this->syncBarangFromVendorInput($validated['nama_menu'], (int) $validated['harga']);

        $menu->update([
            'nama_menu' => $validated['nama_menu'],
            'harga' => $validated['harga'],
            'path_gambar' => $validated['path_gambar'] ?? $menu->path_gambar,
            'is_available' => $request->boolean('is_available', false),
        ]);

        return redirect()->route('admin.vendor.kelola', $vendor->idvendor)->with('success', 'Menu berhasil diperbarui.');
    }

    public function adminDestroyMenu(Vendor $vendor, Menu $menu)
    {
        abort_unless((int) $menu->idvendor === (int) $vendor->idvendor, 404);

        $menu->delete();

        return redirect()->route('admin.vendor.kelola', $vendor->idvendor)->with('success', 'Menu berhasil dihapus.');
    }


    public function adminDestroyOrder(Vendor $vendor, Pesanan $pesanan)
    {
        abort_unless((int) $pesanan->idvendor === (int) $vendor->idvendor, 404);

        $pesanan->details()->delete();
        $pesanan->delete();

        return redirect()->route('admin.vendor.kelola', $vendor->idvendor)->with('success', 'Pesanan berhasil dihapus.');
    }

    private function generateBarangId(): string
    {
        do {
            $id = 'B' . strtoupper(Str::random(7));
        } while (Barang::where('id_barang', $id)->exists());

        return $id;
    }

    private function syncBarangFromVendorInput(string $nama, int $harga): void
    {
        $exists = Barang::where('nama', $nama)
            ->where('harga', $harga)
            ->exists();

        if ($exists) {
            return;
        }

        Barang::create([
            'id_barang' => $this->generateBarangId(),
            'nama' => $nama,
            'harga' => $harga,
        ]);
    }
}
