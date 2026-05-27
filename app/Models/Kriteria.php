<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    /** @use HasFactory<\Database\Factories\KriteriaFactory> */
    use HasFactory;

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'jenis',
        'bobot',
    ];

    public function nilais() { return $this->hasMany(NilaiAlternatif::class); }
}
