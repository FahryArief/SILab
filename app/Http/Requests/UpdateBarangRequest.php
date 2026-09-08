<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'teknisi']);
    }

    public function rules(): array
    {
        $barangId = $this->route('barang')->id;

        return [
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'merk' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'barcode' => 'required|string|unique:barangs,barcode,' . $barangId,
            'kepemilikan' => 'nullable|string|max:255',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'harga' => 'nullable|numeric|min:0',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
