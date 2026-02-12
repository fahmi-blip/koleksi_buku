<?php

namespace App\Http\Controllers;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
public function index()
    {
        $kategori = Kategori::all(); // Mengambil semua data kategori
        return view('pages.kategori', compact('kategori'));
    }

public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:255',]);
        Kategori::create(['nama_kategori'=>$request->nama_kategori]);
        return redirect()->back()->with('success', 'Kategori berhasil ditambah');
    }

}
