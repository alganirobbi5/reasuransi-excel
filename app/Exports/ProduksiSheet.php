<?php

namespace App\Exports;

use App\Models\Produksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProduksiSheet implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Laporan Produksi & Premi Reasur';
    }

    public function headings(): array
    {
        return [
            'No Polis',
            'Nama Tertanggung',
            'Tanggal Lahir',
            'UP Utama',
            'Retention',
            'UP Ceded',
            'Jenis Reasuransi',
            'Premi Reasuransi',
        ];
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return Produksi::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Produksi $p) => [
                $p->no_polis,
                $p->nama_tertanggung,
                $p->tanggal_lahir?->format('Y-m-d'),
                (float) $p->up_utama,
                (float) $p->retention,
                (float) $p->up_ceded,
                $p->jenis_reasuransi,
                (float) $p->premi_reasuransi,
            ]);
    }
}
