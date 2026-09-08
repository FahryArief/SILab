<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPrestasi extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'tahun', 'ikon', 'medali'];
}
