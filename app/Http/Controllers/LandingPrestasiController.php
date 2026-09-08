<?php

namespace App\Http\Controllers;

use App\Models\LandingPrestasi;
use Illuminate\Http\Request;

class LandingPrestasiController extends Controller
{
    public function index()
    {
        $prestasi = LandingPrestasi::latest()->get();
        return view('admin.landing.prestasi', compact('prestasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tahun' => 'required|digits:4',
            'ikon' => 'nullable|string',
            'medali' => 'required|in:gold,silver,bronze,champion',
        ]);

        LandingPrestasi::create($request->all());
        return back()->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tahun' => 'required|digits:4',
            'ikon' => 'nullable|string',
            'medali' => 'required|in:gold,silver,bronze,champion',
        ]);

        LandingPrestasi::findOrFail($id)->update($request->all());
        return back()->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        LandingPrestasi::findOrFail($id)->delete();
        return back()->with('success', 'Prestasi berhasil dihapus.');
    }
}
