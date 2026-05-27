<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaBobotMatrix extends Model
{
    use HasFactory;

    protected $fillable = [
        'matrix',
        'kriteria_ids',
    ];

    protected $casts = [
        'matrix' => 'array',
        'kriteria_ids' => 'array',
    ];
}
