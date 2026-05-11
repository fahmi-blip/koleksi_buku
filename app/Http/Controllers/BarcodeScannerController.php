<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BarcodeScannerController extends Controller
{

	public function index()
	{
		return view('pages.barang');
	}

	public function lookup(Request $request): JsonResponse
	{
		try {
			$validated = $request->validate([
				'barcode' => 'required|string|max:100',
			]);

			$barcode = trim($validated['barcode']);

			// Cari barang berdasarkan id_barang (barcode)
			$barang = Barang::where('id_barang', '=', $barcode, 'and')->first();

			if (! $barang) {
				return response()->json([
					'success' => false,
					'message' => 'Barang tidak ditemukan untuk barcode: ' . $barcode,
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
		} catch (\Exception $e) {
			Log::error('BarcodeScannerController.lookup error: ' . $e->getMessage(), [
				'barcode' => $validated['barcode'] ?? null,
				'exception' => $e,
			]);

			return response()->json([
				'success' => false,
				'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
			], 500);
		}
	}
}

