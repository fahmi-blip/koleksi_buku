<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeCode128;

class BarangController extends Controller
{
    public function index()
    {
        $data_barang = Barang::all();
        return view('pages.barang', compact('data_barang'));
    }

    public function create()
    {
        return view('pages.barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|unique:barang,id_barang|max:8',
            'nama'      => 'required|max:50',
            'harga'     => 'required|integer|min:0',
        ]);

        Barang::create($request->all());
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('pages.barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->all());
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function formCetak(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('barang.index')->with('error', 'Pilih minimal satu barang untuk dicetak.');
        }

        $data_barang = Barang::whereIn('id_barang', $ids)->get();
        return view('pages.barang.cetak', compact('data_barang', 'ids'));
    }

    public function cetakPdf(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:barang,id_barang',
            'start_x' => 'required|integer|min:1|max:3',
            'start_y' => 'required|integer|min:1|max:4',
        ]);

        $ids      = $request->input('ids');
        $startX   = (int) $request->input('start_x');
        $startY   = (int) $request->input('start_y');
        $quantity = $request->input('quantity', []);   

        $barangList = Barang::whereIn('id_barang', $ids)->get()->keyBy('id_barang');

        $labels = [];
        foreach ($ids as $id) {
            $barang = $barangList[$id];
            $qty = isset($quantity[$id]) ? (int) $quantity[$id] : 1;
            $qty = max(1, $qty);
            for ($i = 0; $i < $qty; $i++) {
                $barcode = (new TypeCode128())->getBarcode($barang->id_barang);
                $barcodeRenderer = new PngRenderer();
                $barcodeImage = $barcodeRenderer->render($barcode);
                $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeImage);
                
                $labels[] = [
                    'barang' => $barang,
                    'barcodeSvg' => $barcodeBase64,
                ];
            }
        }

        $startIndex = ($startY - 1) * 5 + ($startX - 1);

        $totalSlots = $startIndex + count($labels);
        $totalPages = (int) ceil($totalSlots / 40);

        $pages = [];
        $labelPointer = 0;
        for ($page = 0; $page < $totalPages; $page++) {
            $pageSlots = [];
            for ($slot = 0; $slot < 40; $slot++) {
                if ($page === 0 && $slot < $startIndex) {
                    $pageSlots[] = null;  
                } else {
                    $pageSlots[] = isset($labels[$labelPointer]) ? $labels[$labelPointer++] : null;
                }
            }
            $pages[] = $pageSlots;
        }

        $pdf = Pdf::loadView('pages.label_pdf', compact('pages'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('label_harga.pdf');
    }
}
