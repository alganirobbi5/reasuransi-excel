<?php

namespace App\Exports;

use App\Models\Klaim;
use App\Models\Produksi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RingkasanKeuanganSheet implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Ringkasan Akun Keuangan';
    }

    public function headings(): array
    {
        return [
            'Keterangan',
            'Nilai',
        ];
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        // Kerangka sementara: hanya agregat dasar yang terverifikasi.
        // Formula bisnis final menyusul setelah template diinspeksi.
        return new Collection([
            ['Jumlah Data Produksi', Produksi::count()],
            ['Total Premi Reasuransi', (float) Produksi::sum('premi_reasuransi')],
            ['Jumlah Data Klaim', Klaim::count()],
            ['Total Recovery Klaim', (float) Klaim::sum('recovery')],
        ]);
    }
}
