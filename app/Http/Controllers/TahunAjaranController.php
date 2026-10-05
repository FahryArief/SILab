<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjarans = TahunAjaran::latest()->get();
        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun' => 'required|string|max:255',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        TahunAjaran::create($request->only(['nama_tahun', 'semester', 'tanggal_mulai', 'tanggal_selesai']));

        return redirect()->back()->with('success', 'Data Tahun Ajaran berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tahun' => 'required|string|max:255',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($request->only(['nama_tahun', 'semester', 'tanggal_mulai', 'tanggal_selesai']));

        return redirect()->back()->with('success', 'Data Tahun Ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        if ($tahunAjaran->is_active) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Tahun Ajaran yang sedang aktif.');
        }
        $tahunAjaran->delete();

        return redirect()->back()->with('success', 'Data Tahun Ajaran berhasil dihapus.');
    }

    /**
     * Set active academic year
     */
    public function activate($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->activate();

        return redirect()->back()->with('success', 'Tahun Ajaran '.$tahunAjaran->nama_tahun.' ('.$tahunAjaran->semester.') berhasil diaktifkan.');
    }
}
