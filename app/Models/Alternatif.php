<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    /** @use HasFactory<\Database\Factories\AlternatifFactory> */
    use HasFactory;

    protected $fillable = [
        'kode_alternatif',
        'nama_pelanggan',
    ];

    public function nilais() { return $this->hasMany(NilaiAlternatif::class); }
}
