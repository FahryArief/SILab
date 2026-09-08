<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
    public function store(StoreBarangRequest $request)
    {
        DB::transaction(function() use ($request) {
            $default_foto = null;
            if ($request->hasFile('foto_barang')) {
                $foto = $request->file('foto_barang');
                $default_foto = Str::uuid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('foto_barang', $default_foto, 'public');
            }

            foreach ($request->items as $item) {
                // Gunakan foto default jika item tidak punya foto khusus
                $foto_item = $default_foto;

                if (isset($item['foto']) && $request->hasFile("items.{$item['kode_inventaris']}.foto")) {
                    $fotoFile = $request->file("items.{$item['kode_inventaris']}.foto");
                    $foto_item = Str::uuid() . '.' . $fotoFile->getClientOriginalExtension();
                    $fotoFile->storeAs('foto_barang', $foto_item, 'public');
                }

                Barang::create([
                    'nama_barang' => $request->nama_barang,
                    'kategori_id' => $request->kategori_id,
                    'ruangan_id'  => $item['ruangan_id'] ?? $request->ruangan_id,
                    'barcode'     => $item['kode_inventaris'],
                    'foto_barang' => $foto_item,
                    'kondisi'     => $item['kondisi'],
                    'kepemilikan' => $item['kepemilikan'],
                    'status_peminjaman' => 'Tersedia', // Default
                    'merk'        => $item['merk'] ?? null,
                    'harga'       => $item['harga'] ?? null,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Data Barang berhasil ditambahkan!');
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
    public function update(UpdateBarangRequest $request, $id)
    {
        $barang = Barang::findOrFail($id);

        DB::transaction(function() use ($request, $barang) {
            $nama_foto = $barang->foto_barang;

            if ($request->hasFile('foto_barang')) {
                // Hapus foto lama jika bukan null/bawaan seeder (kalau mau aman periksa exists)
                if ($nama_foto && Storage::disk('public')->exists('foto_barang/' . $nama_foto)) {
                    Storage::disk('public')->delete('foto_barang/' . $nama_foto);
                }

                $foto = $request->file('foto_barang');
                $nama_foto = Str::uuid() . '.' . $foto->getClientOriginalExtension();
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
        });

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui!');
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
