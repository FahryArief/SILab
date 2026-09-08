<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if ($user && $user->role !== 'peminjam') {
                        $fail('Akun yang dipilih harus memiliki role peminjam.');
                    }
                },
            ],
            'nama_peminjam' => 'required_without:user_id|nullable|string|max:255',
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'exists:barangs,id|distinct',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}
