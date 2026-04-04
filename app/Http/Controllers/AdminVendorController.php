<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AdminVendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('users')->orderBy('nama_vendor')->get();

        return view('pages.admin.vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('pages.admin.vendor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255|unique:vendor,nama_vendor',
        ]);

        Vendor::create($validated);

        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor)
    {
        return view('pages.admin.vendor.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255|unique:vendor,nama_vendor,' . $vendor->idvendor . ',idvendor',
        ]);

        $vendor->update($validated);

        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();

            return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.vendor.index')->with('error', 'Vendor tidak bisa dihapus karena masih dipakai oleh data menu atau pesanan.');
        }
    }
}