<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BarcodeScannerController extends Controller
{
	public function index()
	{
		return view('pages.barcode-scanner');
	}

	public function lookup(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'barcode' => 'required|string|max:100',
		]);

		$barcode = trim($validated['barcode']);

		// Cari barang berdasarkan id_barang (barcode)
		$barang = Barang::where('id_barang', $barcode)->first();

		if (! $barang) {
			return response()->json([
				'success' => false,
				'message' => 'Barang tidak ditemukan',
			], 404);
		}

		return response()->json([
			'success' => true,
			'data' => [
				'id_barang' => $barang->id_barang,
				'nama' => $barang->nama,
				'harga' => (int) $barang->harga,
			],
		]);
	}
}

