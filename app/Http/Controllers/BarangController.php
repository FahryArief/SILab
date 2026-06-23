<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use Illuminate\Support\Facades\DB;
use App\Exports\BarangExport;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['kategori', 'ruangan'])
                    ->orderBy('nama_barang')
                    ->get()
                    ->groupBy('nama_barang');
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        return view('operator.barang.index', compact('barangs', 'kategoris', 'ruangans'));
    }

    // 1. Menampilkan Form Tambah Barang
    public function create()
    {
        $kategoris = Kategori::all();
        $ruangans = Ruangan::all();
        return view('operator.barang.create', compact('kategoris', 'ruangans'));
    }

    // 2. Menyimpan Data ke Database (Multi Item)
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'ruangan_id' => 'required', // Ini ruangan default
            'singkatan' => 'required',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'items' => 'required|array|min:1',
            'items.*.kode_inventaris' => 'required|unique:barangs,barcode',
            'items.*.kondisi' => 'required',
            'items.*.kepemilikan' => 'required',
            'items.*.foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $default_foto = null;
        if ($request->hasFile('foto_barang')) {
            $foto = $request->file('foto_barang');
            $default_foto = time() . '_default_' . $foto->getClientOriginalName();
            $foto->storeAs('foto_barang', $default_foto, 'public');
        }

        DB::transaction(function() use ($request, $default_foto) {
            foreach ($request->items as $index => $item) {
                
                // Cek apakah item punya ruangan custom, jika tidak pakai ruangan default
                $ruangan_id = !empty($item['ruangan_id']) ? $item['ruangan_id'] : $request->ruangan_id;
                
                // Cek apakah item punya foto custom, jika tidak pakai foto default
                $foto_item = $default_foto;
                if (isset($item['foto']) && $request->hasFile("items.{$index}.foto")) {
                    $f = $request->file("items.{$index}.foto");
                    $foto_item = time() . "_item_{$index}_" . $f->getClientOriginalName();
                    $f->storeAs('foto_barang', $foto_item, 'public');
                }

                Barang::create([
                    'nama_barang' => $request->nama_barang,
                    'kategori_id' => $request->kategori_id,
                    'ruangan_id' => $ruangan_id,
                    'merk' => $item['merk'] ?? null,
                    'deskripsi' => $request->deskripsi,
                    'barcode' => $item['kode_inventaris'],
                    'foto_barang' => $foto_item,
                    'kepemilikan' => $item['kepemilikan'],
                    'kondisi' => $item['kondisi'],
                    'harga' => $item['harga'] ?? null,
                    'status_peminjaman' => 'Tersedia'
                ]);
            }
        });

        return redirect()->route('barang.index')->with('success', count($request->items) . ' Barang fisik unik berhasil ditambahkan!');
    }

    // 3. Menampilkan Form Edit Barang
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategoris = Kategori::all();
        $ruangans = Ruangan::all();
        return view('operator.barang.edit', compact('barang', 'kategoris', 'ruangans'));
    }

    // 4. Menyimpan Perubahan Data (Update Item Fisik)
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'ruangan_id' => 'required',
            'barcode' => 'required|unique:barangs,barcode,' . $barang->id,
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kondisi' => 'required',
            'kepemilikan' => 'required',
            'status_peminjaman' => 'required|in:Tersedia,Dipinjam,Pemeliharaan',
        ]);

        $nama_foto = $barang->foto_barang;

        if ($request->hasFile('foto_barang')) {
            if ($nama_foto && Storage::disk('public')->exists('foto_barang/' . $nama_foto)) {
                Storage::disk('public')->delete('foto_barang/' . $nama_foto);
            }
            $foto = $request->file('foto_barang');
            $nama_foto = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('foto_barang', $nama_foto, 'public');
        }

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'ruangan_id' => $request->ruangan_id,
            'barcode' => $request->barcode,
            'merk' => $request->merk,
            'deskripsi' => $request->deskripsi,
            'foto_barang' => $nama_foto,
            'kondisi' => $request->kondisi,
            'kepemilikan' => $request->kepemilikan,
            'harga' => $request->harga,
            'status_peminjaman' => $request->status_peminjaman,
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang fisik berhasil diperbarui!');
    }

    /**
     * Inline update (AJAX) — update single fields without page reload
     */
    public function inlineUpdate(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $allowed = ['merk', 'kondisi', 'kepemilikan', 'deskripsi', 'harga', 'status_peminjaman', 'kategori_id', 'ruangan_id'];
        $data = $request->only($allowed);

        // Handle foto upload
        if ($request->hasFile('foto_barang')) {
            $request->validate(['foto_barang' => 'image|mimes:jpeg,png,jpg|max:2048']);

            // Delete old photo if it's unique to this item
            if ($barang->foto_barang && Storage::disk('public')->exists('foto_barang/' . $barang->foto_barang)) {
                // Only delete if no other barang uses this same photo
                $othersUsingPhoto = Barang::where('foto_barang', $barang->foto_barang)->where('id', '!=', $barang->id)->count();
                if ($othersUsingPhoto === 0) {
                    Storage::disk('public')->delete('foto_barang/' . $barang->foto_barang);
                }
            }

            $foto = $request->file('foto_barang');
            $nama_foto = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('foto_barang', $nama_foto, 'public');
            $data['foto_barang'] = $nama_foto;
        }

        $barang->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil diperbarui',
            'foto_url' => $barang->foto_barang ? asset('storage/foto_barang/' . $barang->foto_barang) : null,
        ]);
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        if ($barang->peminjamans()->count() > 0) {
            return redirect()->route('barang.index')->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat peminjaman. Ubah statusnya jika sudah tidak digunakan.');
        }

        // Hapus foto jika ada
        if ($barang->foto_barang && Storage::disk('public')->exists('foto_barang/' . $barang->foto_barang)) {
            Storage::disk('public')->delete('foto_barang/' . $barang->foto_barang);
        }

        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new BarangExport, 'Data_Barang_'.date('Ymd_His').'.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new BarangImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data Barang berhasil diimport!');
    }
}
