<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Picqer\Barcode\Types\TypeCode128;
use Picqer\Barcode\Renderers\HtmlRenderer;

class BarcodeController extends Controller
{
    public function index(Barang $barang)
    {
        $barcode = (new TypeCode128())->getBarcode($barang->id_barang);

        $renderer = new HtmlRenderer();

        return response()->view('pages.barcode', [
            'barang' => $barang,
            'barcodeHtml' => $renderer->render($barcode),
        ]);
    }
}
