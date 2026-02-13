<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    public function index()
    {
        // Eager loading 'kategori' agar efisien
        $data_buku = Buku::with('kategori')->get();
        return view('pages.buku', compact('data_buku'));
    }

    public function create()
    {
        // Ambil data kategori untuk dropdown
        $categories = Kategori::all();
        return view('pages.buku.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'        => 'required|unique:buku,kode|max:20',
            'judul'       => 'required|max:100',
            'pengarang'   => 'required|max:100',
            'kategori_id' => 'exists:idkategori,id',
        ]);

        Buku::create($request->all());
        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan');
    }

    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        $categories = Kategori::all();
        return view('pages.buku.edit', compact('buku', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode'        => 'required','max:20',Rule::unique('buku','kode')->ignore($id, 'idbuku'),
            'judul'       => 'required|max:100',
            'pengarang'   => 'required|max:100',
            'kategori_id' => 'exists:idkategori,id',
        ]);

        $buku = Buku::findOrFail($id);
        $buku->update($request->all());
        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui');
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus');
    }
}