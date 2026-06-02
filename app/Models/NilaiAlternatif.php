<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Periode;

class NilaiAlternatif extends Model
{
    /** @use HasFactory<\Database\Factories\NilaiAlternatifFactory> */
    use HasFactory;

    protected $fillable = [
        'alternatif_id',
        'kriteria_id',
        'periode_id',
        'nilai',
    ];

    public function alternatif() { return $this->belongsTo(Alternatif::class); }
    public function kriteria() { return $this->belongsTo(Kriteria::class); }
    public function periode() { return $this->belongsTo(Periode::class); }
}
