<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingDokumentasi extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'tanggal', 'tag', 'gambar'];
}
