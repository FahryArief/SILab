<?php

namespace App\Http\Controllers;

use App\Models\Pemeliharaan;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class PemeliharaanController extends Controller
{
    /**
     * Daftar riwayat pemeliharaan (terbaru dulu), + form tambah.
     */
    public function index()
    {
        $pemeliharaans = Pemeliharaan::with(['barang:id,nama_barang', 'ruangan:id,nama_ruangan', 'teknisi:id,name'])
            ->latest('tanggal_pemeliharaan')
            ->latest('id')
            ->paginate(25);

        $barangs = Barang::select(['id', 'nama_barang', 'barcode'])->orderBy('nama_barang')->get();
        $ruangans = Ruangan::select(['id', 'nama_ruangan'])->orderBy('nama_ruangan')->get();

        return view('admin.pemeliharaan.index', compact('pemeliharaans', 'barangs', 'ruangans'));
    }

    /**
     * Catat satu riwayat pemeliharaan baru untuk Barang atau Ruangan.
     *
     * Kalau jenis_aset = barang dan kondisi_sesudah diisi, kondisi Barang di
     * master langsung ikut diupdate (dan status_peminjaman dikembalikan ke
     * "Tersedia" kalau sebelumnya "Pemeliharaan") — supaya teknisi tidak
     * perlu mengubah dua tempat untuk hal yang sama.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_aset' => 'required|in:barang,ruangan',
            'barang_id' => 'required_if:jenis_aset,barang|nullable|exists:barangs,id',
            'ruangan_id' => 'required_if:jenis_aset,ruangan|nullable|exists:ruangans,id',
            'tanggal_pemeliharaan' => 'required|date',
            'jenis_perbaikan' => 'required|string|max:255',
            'kondisi_sebelum' => 'nullable|string|max:255',
            'kondisi_sesudah' => 'nullable|string|max:255',
            'biaya' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if ($request->jenis_aset === 'barang') {
            $barang = Barang::findOrFail($request->barang_id);
            $namaAset = $barang->nama_barang . ' (' . $barang->barcode . ')';
        } else {
            $barang = null;
            $ruangan = Ruangan::findOrFail($request->ruangan_id);
            $namaAset = $ruangan->nama_ruangan;
        }

        Pemeliharaan::create([
            'jenis_aset' => $request->jenis_aset,
            'barang_id' => $request->jenis_aset === 'barang' ? $request->barang_id : null,
            'ruangan_id' => $request->jenis_aset === 'ruangan' ? $request->ruangan_id : null,
            'nama_aset_snapshot' => $namaAset,
            'tanggal_pemeliharaan' => $request->tanggal_pemeliharaan,
            'jenis_perbaikan' => $request->jenis_perbaikan,
            'kondisi_sebelum' => $request->kondisi_sebelum,
            'kondisi_sesudah' => $request->kondisi_sesudah,
            'biaya' => $request->biaya,
            'teknisi_id' => auth()->id(),
            'catatan' => $request->catatan,
            'tahun_ajaran_id' => $tahunAjaranAktif?->id,
        ]);

        if ($request->jenis_aset === 'barang' && $request->filled('kondisi_sesudah')) {
            $update = ['kondisi' => $request->kondisi_sesudah];
            if ($barang->status_peminjaman === 'Pemeliharaan') {
                $update['status_peminjaman'] = 'Tersedia';
            }
            $barang->update($update);
        }

        return redirect()->back()->with('success', 'Riwayat pemeliharaan "' . $namaAset . '" berhasil disimpan.');
    }

    /**
     * Hapus satu catatan riwayat pemeliharaan (tidak mengubah kondisi aset).
     */
    public function destroy($id)
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $pemeliharaan->delete();

        return redirect()->back()->with('success', 'Riwayat pemeliharaan berhasil dihapus.');
    }
}
