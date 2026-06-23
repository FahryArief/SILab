<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return view('operator.kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required']);
        Kategori::create(['nama_kategori' => $request->nama_kategori]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    // Tambahkan 2 fungsi ini di bawah fungsi store()
    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('operator.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_kategori' => 'required']);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Kategori::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
