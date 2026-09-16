<?php

namespace App\Exports;

use App\Models\Produksi;
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

class ProduksiSheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    public const SHEET_TITLE = 'Laporan Produksi & Premi Reasur';

    public const REPORT_TITLE = 'Laporan Produksi & Premi Reasuransi (Borderaux Premi)';

    public const DATA_START_ROW = 4;

    public const HEADERS = [
        'No Polis',
        'Nama Tertanggung',
        'Tanggal Lahir',
        'Uang Pertanggungan (UP Utama)',
        'Sendiri (Retention)',
        'UP direasuransikan (Ceded)',
        'Jenis Reasuransi',
        'Premi Reasuransi',
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
            'A' => 9.66,
            'D' => 19.5,
            'F' => 16.83,
            'G' => 12.16,
            'H' => 17.16,
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
        return $this->rows ??= Produksi::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Produksi $p) => [
                $p->no_polis,
                $p->nama_tertanggung,
                // Template leaves date format unspecified (General + empty
                // samples), so emit an unambiguous ISO string instead of
                // inventing an Excel date format.
                $p->tanggal_lahir?->format('Y-m-d'),
                (float) $p->up_utama,
                (float) $p->retention,
                (float) $p->up_ceded,
                $p->jenis_reasuransi,
                (float) $p->premi_reasuransi,
            ]);
    }

    private function applyTemplate(Worksheet $ws): void
    {
        $count = $this->rows()->count();
        $dataEnd = self::DATA_START_ROW + $count - 1;
        $totalRow = self::DATA_START_ROW + $count;

        // Writer placed data (if any) at rows 1..n; shift it down so
        // row 1 = title, row 2 = spacer, row 3 = headers, row 4+ = data.
        $ws->insertNewRowBefore(1, 3);

        // Workbook default font per template (Aptos Narrow 12 everywhere
        // unless a cell below overrides it).
        $ws->getParent()->getDefaultStyle()->getFont()
            ->setName('Aptos Narrow')
            ->setSize(12);

        // Title A1:H1.
        $ws->mergeCells('A1:H1');
        $ws->setCellValue('A1', self::REPORT_TITLE);
        $ws->getRowDimension(1)->setRowHeight(22);
        $ws->getStyle('A1')->applyFromArray([
            'font' => ['size' => 16, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A6A6A6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
        ]);

        // Headers row 3.
        $ws->getRowDimension(3)->setRowHeight(34);
        foreach (self::HEADERS as $i => $header) {
            $ws->setCellValue(Coordinate::stringFromColumnIndex($i + 1).'3', $header);
        }
        $ws->getStyle('A3:H3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Data table borders.
        if ($count > 0) {
            $ws->getStyle('A'.self::DATA_START_ROW.':H'.$dataEnd)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
            // Accounting format (integers, no decimals) per template.
            $ws->getStyle('D'.self::DATA_START_ROW.':F'.$dataEnd)
                ->getNumberFormat()->setFormatCode(self::ACCOUNTING_FORMAT);
        }

        // Total row follows the data; never a hardcoded range.
        $ws->getRowDimension($totalRow)->setRowHeight(19);
        $ws->setCellValue('A'.$totalRow, 'Total');
        if ($count > 0) {
            $ws->setCellValue('H'.$totalRow, '=SUM(H'.self::DATA_START_ROW.':H'.$dataEnd.')');
        } else {
            // =SUM(H4:H3) would be an invalid range, so an empty dataset
            // gets a static 0 instead of a formula.
            $ws->setCellValue('H'.$totalRow, 0);
        }
        $ws->getStyle('H'.$totalRow)->getNumberFormat()->setFormatCode(self::ACCOUNTING_FORMAT);
        $ws->getStyle('A'.$totalRow.':H'.$totalRow)->applyFromArray([
            'font' => ['size' => 14, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '595959']],
        ]);
    }
}
