<?php

namespace App\Http\Controllers;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
public function index()
    {
        // Mengambil buku beserta data kategorinya (Eager Loading)
        $bukus = Buku::with('kategori')->get();
        $kategoris = Kategori::all(); // Untuk dropdown di form tambah
        return view('pages.buku', compact('bukus', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idkategori' => 'required|exists:kategoris,id',
            'kode' => 'required|unique:bukus,kode',
            'judul' => 'required',
            'pengarang' => 'required',
        ]);

        Buku::create($request->all());
        return redirect()->back()->with('success', 'Buku berhasil ditambahkan');
    }
}
