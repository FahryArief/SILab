<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'nama_peminjam'   => 'required_without:user_id|nullable|string|max:255',
            'ruangan_id'      => 'required|exists:ruangans,id',
            'tanggal_booking' => 'required|date',
            'waktu_mulai'     => 'required',
            'waktu_selesai'   => 'required|after:waktu_mulai',
            'keperluan'       => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'nama_peminjam.required_without' => 'Nama peminjam wajib diisi jika tidak memilih akun mahasiswa!',
        ];
    }
}
