<?php

namespace App\Exports;

use App\Models\Klaim;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KlaimSheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    // Clean name (no trailing whitespace) per Sheet 2 foundation decision.
    public const SHEET_TITLE = 'Laporan Klaim Reasuransi';

    public const REPORT_TITLE = 'Laporan Klaim Reasuransi (Borderaux Klaim)';

    public const DATA_START_ROW = 4;

    // Verbatim template headers, including the "Reasuansi" typo.
    public const HEADERS = [
        'No Klaim',
        'No Polis',
        'Nama Tertanggung',
        'Penyebab Meninggal/Klaim',
        'Tanggal Lahir',
        'Total Nilai Klaim',
        'Uang Pertanggungan (UP Utama)',
        'Porsi Klaim Reasuansi (Recovery)',
        'UP direasuransikan (Ceded)',
        'Status Klaim',
    ];

    public const ACCOUNTING_FORMAT = '_(* #,##0_);_(* \(#,##0\);_(* "-"_);_(@_)';

    private ?Collection $rows = null;

    public function title(): string
    {
        return self::SHEET_TITLE;
    }

    public function columnWidths(): array
    {
        return [
            'D' => 14.16,
            'G' => 18.83,
            'H' => 19.66,
            'I' => 17.66,
            'J' => 22.66,
        ];
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->applyTemplate($event->sheet->getDelegate());
            },
        ];
    }

    /**
     * Single query per export instance, shared by collection() and the
     * AfterSheet handler so dynamic row math never needs a second query.
     */
    private function rows(): Collection
    {
        return $this->rows ??= Klaim::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Klaim $k) => [
                $k->no_klaim,
                $k->no_polis,
                $k->nama_tertanggung,
                $k->penyebab_klaim,
                // Template leaves date format unspecified (General + empty
                // samples), so emit an unambiguous ISO string instead of
                // inventing an Excel date format.
                $k->tanggal_lahir?->format('Y-m-d'),
                (float) $k->total_nilai_klaim,
                (float) $k->up_utama,
                (float) $k->recovery,
                (float) $k->up_ceded,
                $k->status_klaim,
            ]);
    }

    private function applyTemplate(Worksheet $ws): void
    {
        $count = $this->rows()->count();
        $dataEnd = self::DATA_START_ROW + $count - 1;

        // Writer placed data (if any) at rows 1..n; shift it down so
        // row 1 = title, row 2 = spacer, row 3 = headers, row 4+ = data.
        // Sheet 2 has no total row and no formulas by template design.
        $ws->insertNewRowBefore(1, 3);

        // Workbook default font per template (Aptos Narrow 12 everywhere
        // unless a cell below overrides it).
        $ws->getParent()->getDefaultStyle()->getFont()
            ->setName('Aptos Narrow')
            ->setSize(12);

        // Title A1:J1.
        $ws->mergeCells('A1:J1');
        $ws->setCellValue('A1', self::REPORT_TITLE);
        $ws->getRowDimension(1)->setRowHeight(22);
        $ws->getStyle('A1')->applyFromArray([
            'font' => ['size' => 16, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A6A6A6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
        ]);

        // Headers row 3. Template quirk: A3 uses general (left) alignment
        // while B3:J3 are centered; all share wrap, vertical center and
        // thin borders.
        $ws->getRowDimension(3)->setRowHeight(51);
        foreach (self::HEADERS as $i => $header) {
            $ws->setCellValue(Coordinate::stringFromColumnIndex($i + 1).'3', $header);
        }
        $ws->getStyle('B3:J3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
        $ws->getStyle('A3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_GENERAL,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Data table borders + accounting format (integers, no decimals)
        // on nominal columns F, G, H, I per template.
        if ($count > 0) {
            $ws->getStyle('A'.self::DATA_START_ROW.':J'.$dataEnd)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
            foreach (['F', 'G', 'H', 'I'] as $col) {
                $ws->getStyle($col.self::DATA_START_ROW.':'.$col.$dataEnd)
                    ->getNumberFormat()->setFormatCode(self::ACCOUNTING_FORMAT);
            }
        }
    }
}
