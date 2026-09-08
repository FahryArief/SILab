<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'teknisi']);
    }

    public function rules(): array
    {
        return [
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'singkatan' => 'required|string|max:255',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'items' => 'required|array|min:1',
            'items.*.kode_inventaris' => 'required|string|unique:barangs,barcode',
            'items.*.kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'items.*.kepemilikan' => 'required|string',
            'items.*.foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'items.*.merk' => 'nullable|string|max:255',
            'items.*.harga' => 'nullable|numeric|min:0',
            'items.*.ruangan_id' => 'nullable|exists:ruangans,id',
        ];
    }
}
