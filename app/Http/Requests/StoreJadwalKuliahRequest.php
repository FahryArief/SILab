<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJadwalKuliahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'teknisi']);
    }

    public function rules(): array
    {
        return [
            'ruangan_id' => 'required|exists:ruangans,id',
            'hari' => 'required|string',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'mata_kuliah' => 'required|string|max:255',
            'dosen' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ];
    }
}
