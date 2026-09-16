<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $fillable = [
        'no_polis',
        'nama_tertanggung',
        'tanggal_lahir',
        'up_utama',
        'retention',
        'up_ceded',
        'jenis_reasuransi',
        'premi_reasuransi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'up_utama' => 'decimal:2',
        'retention' => 'decimal:2',
        'up_ceded' => 'decimal:2',
        'premi_reasuransi' => 'decimal:2',
    ];
}