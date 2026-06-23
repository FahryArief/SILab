<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class ScanBarangController extends Controller
{
    /**
     * Halaman publik: Scan QR Barang
     * Menampilkan detail barang berdasarkan barcode (kode inventaris)
     */
    public function show($barcode)
    {
        $barang = Barang::with(['kategori:id,nama_kategori', 'ruangan:id,nama_ruangan,lokasi'])
            ->where('barcode', $barcode)
            ->firstOrFail();

        return view('scan.barang', compact('barang'));
    }
}
