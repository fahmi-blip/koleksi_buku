<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
   public function homePage()
    {
        return view('pages.home');
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