<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'teknisi']);
    }

    public function rules(): array
    {
        return [
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas'    => 'nullable|integer|min:1',
            'lokasi'       => 'nullable|string|max:255',
            'keterangan'   => 'nullable|string',
            'fasilitas'    => 'nullable|string',
            'foto_ruangan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
