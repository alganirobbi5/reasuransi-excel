<?php

namespace App\Exports;

use App\Models\Produksi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RingkasanKeuanganSheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    public const SHEET_TITLE = 'Ringkasan Akun Keuangan';

    public const REPORT_TITLE = 'Ringkasan Akun Keuangan';

    public const ACCOUNTING_FORMAT = '_(* #,##0_);_(* \(#,##0\);_(* "-"_);_(@_)';

    // Verbatim template labels (typos and trailing whitespace preserved).
    public const BLOCK_LABELS = [
        'B' => 'Total Premi',
        'E' => 'Reinsurance Commision',
        'H' => 'Total Premi Reasuransi/Netto',
        'K' => 'Recovery',
        'N' => 'Saldo Akhir',
    ];

    public const DETAIL_LABELS = [
        'B' => 'Premi Reasuransi Gross',
        'E' => 'Dikurangi Komisi reasuransi',
        'H' => 'Premi Netto',
        'K' => 'Dikurangi Total Klaim Reasunasi ',
        'N' => 'Perusahaan Reasuransi harus membayar jumlah ini kepada Cedant karena nilai klaim lebih besar dari premi)',
    ];

    public const BLOCK_COLUMNS = ['B', 'E', 'H', 'K', 'N'];

    private ?int $productionCount = null;

    public function title(): string
    {
        return self::SHEET_TITLE;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'D' => 5.83,
            'G' => 5.83,
            'J' => 5.83,
            'M' => 5.83,
            'I' => 15,
            'O' => 17.66,
        ];
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        // Form sheet, not a data table: no data rows by design.
        return new Collection();
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
     * ProduksiSheet places its total at row 4 + production count
     * (DATA_START_ROW 4, one row per record, static 0 when empty),
     * so the cross-sheet reference must be resolved at runtime.
     */
    private function productionTotalCell(): string
    {
        $this->productionCount ??= Produksi::count();

        return "'".ProduksiSheet::SHEET_TITLE."'!H".(ProduksiSheet::DATA_START_ROW + $this->productionCount);
    }

    private function applyTemplate(Worksheet $ws): void
    {
        $ws->getParent()->getDefaultStyle()->getFont()
            ->setName('Aptos Narrow')
            ->setSize(12);

        $ws->getRowDimension(1)->setRowHeight(22);
        $ws->getRowDimension(11)->setRowHeight(19);
        $ws->getRowDimension(12)->setRowHeight(19);

        // Title A1:O1.
        $ws->mergeCells('A1:O1');
        $ws->setCellValue('A1', self::REPORT_TITLE);
        $ws->getStyle('A1')->applyFromArray([
            'font' => ['size' => 16, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BFBFBF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Logic label A4:A11.
        $ws->mergeCells('A4:A11');
        $ws->setCellValue('A4', 'Logic/kalkulasi');
        $ws->getStyle('A4:A11')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (self::BLOCK_COLUMNS as $col) {
            $next = chr(ord($col) + 1);

            // Block label row 4 (white on dark; not bold per template).
            $ws->mergeCells("{$col}4:{$next}4");
            $ws->setCellValue("{$col}4", self::BLOCK_LABELS[$col]);
            $ws->getStyle("{$col}4:{$next}4")->applyFromArray([
                'font' => ['color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '404040']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Mirror display rows 5-6 (live formulas, bold).
            $ws->mergeCells("{$col}5:{$next}6");
            $ws->getStyle("{$col}5:{$next}6")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $ws->getStyle("{$col}5:{$next}6")->getNumberFormat()->setFormatCode(self::ACCOUNTING_FORMAT);

            // Detail label rows 7-10 (yellow area; template merges the
            // whole rows 7-10 block vertically per label).
            $ws->mergeCells("{$col}7:{$next}10");
            $ws->setCellValue("{$col}7", self::DETAIL_LABELS[$col]);
            $ws->getStyle("{$col}7:{$next}10")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
                'alignment' => [
                    'horizontal' => $col === 'N' ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            // Result row 11 (blue, bold 14, top border only per template).
            $ws->mergeCells("{$col}11:{$next}11");
            $ws->getStyle("{$col}11:{$next}11")->applyFromArray([
                'font' => ['size' => 14, 'bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A6C9EC']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Mirror formulas (row 5 blocks reference row 11/12 results).
        $ws->setCellValue('B5', '=B11');
        $ws->setCellValue('E5', '=E12');
        $ws->setCellValue('H5', '=H11');
        $ws->setCellValue('K5', '=K11');
        $ws->setCellValue('N5', '=N11');

        // Result row 11.
        $ws->setCellValue('B11', '='.$this->productionTotalCell());
        $ws->setCellValue('E11', 0.1);
        $ws->getStyle('E11:F11')->getNumberFormat()->setFormatCode('0%');
        $ws->setCellValue('H11', '=B11-E12');
        // K11 intentionally left blank (audit decision: no evidence for
        // recovery source; template's 700000000 is an unverified sample).
        $ws->setCellValue('N11', '=K11-H11');
        foreach (['B', 'H', 'K', 'N'] as $col) {
            $next = chr(ord($col) + 1);
            $ws->getStyle("{$col}11:{$next}11")->getNumberFormat()->setFormatCode(self::ACCOUNTING_FORMAT);
        }

        // Commission amount row 12.
        $ws->mergeCells('E12:F12');
        $ws->setCellValue('E12', '=B11*E11');
        $ws->getStyle('E12:F12')->applyFromArray([
            'font' => ['size' => 14, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A6C9EC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getStyle('E12:F12')->getNumberFormat()->setFormatCode('0');
    }
}
