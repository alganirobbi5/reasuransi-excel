<?php

namespace App\Exports;

use App\Models\Klaim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class KlaimSheet implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Laporan Klaim Reasuransi';
    }

    public function headings(): array
    {
        return [
            'No Klaim',
            'No Polis',
            'Nama Tertanggung',
            'Penyebab Klaim',
            'Tanggal Lahir',
            'Total Nilai Klaim',
            'UP Utama',
            'Recovery',
            'UP Ceded',
            'Status Klaim',
        ];
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return Klaim::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Klaim $k) => [
                $k->no_klaim,
                $k->no_polis,
                $k->nama_tertanggung,
                $k->penyebab_klaim,
                $k->tanggal_lahir?->format('Y-m-d'),
                (float) $k->total_nilai_klaim,
                (float) $k->up_utama,
                (float) $k->recovery,
                (float) $k->up_ceded,
                $k->status_klaim,
            ]);
    }
}
