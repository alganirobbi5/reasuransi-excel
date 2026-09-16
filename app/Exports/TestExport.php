<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class TestExport implements FromCollection
{
    public function collection(): \Illuminate\Support\Enumerable
    {
        return new Collection([
            [
                'No Polis',
                'Nama Tertanggung',
                'Premi Reasuransi',
            ],
            [
                'POL-001',
                'Test Tertanggung',
                1500000,
            ],
        ]);
    }
}