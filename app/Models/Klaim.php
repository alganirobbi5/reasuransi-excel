<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klaim extends Model
{
    protected $fillable = [
        'no_klaim',
        'no_polis',
        'nama_tertanggung',
        'penyebab_klaim',
        'tanggal_lahir',
        'total_nilai_klaim',
        'up_utama',
        'recovery',
        'up_ceded',
        'status_klaim',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'total_nilai_klaim' => 'decimal:2',
        'up_utama' => 'decimal:2',
        'recovery' => 'decimal:2',
        'up_ceded' => 'decimal:2',
    ];
}