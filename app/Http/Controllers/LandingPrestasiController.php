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
            'medali' => 'required|string|max:50',
            'medali_custom' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        if ($data['medali'] === 'lainnya') {
            $data['medali'] = $request->medali_custom;
        }

        LandingPrestasi::create($data);
        return back()->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tahun' => 'required|digits:4',
            'ikon' => 'nullable|string',
            'medali' => 'required|string|max:50',
            'medali_custom' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        if ($data['medali'] === 'lainnya') {
            $data['medali'] = $request->medali_custom;
        }

        LandingPrestasi::findOrFail($id)->update($data);
        return back()->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        LandingPrestasi::findOrFail($id)->delete();
        return back()->with('success', 'Prestasi berhasil dihapus.');
    }
}
