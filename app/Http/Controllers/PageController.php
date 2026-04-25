<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Barang;
use App\Models\Vendor;

class PageController extends Controller
{
   public function homePage()
    {
        $bukuCount = Buku::count();
        $barangCount = Barang::count();
        $vendorCount = Vendor::count();
        
        return view('pages.home', compact('bukuCount', 'barangCount', 'vendorCount'));
    }

    public function latihanTable()
    {
        return view('pages.latihan.table');
    }

    public function latihanDatatables()
    {
        return view('pages.latihan.datatables');
    }

    public function latihanSelect()
    {
        return view('pages.latihan.select');
    }
    public function wilayah()
    {
        return view('pages.wilayah');
    }
}