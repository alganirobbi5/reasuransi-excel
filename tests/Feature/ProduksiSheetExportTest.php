<?php

namespace Tests\Feature;

use App\Exports\ProduksiSheet;
use App\Exports\ReasuransiExport;
use App\Models\Produksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ProduksiSheetExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduksi(array $overrides = []): Produksi
    {
        return Produksi::create(array_merge([
            'no_polis' => 'POL-001',
            'nama_tertanggung' => 'Budi Santoso',
            'tanggal_lahir' => '1990-05-10',
            'up_utama' => 1000000000,
            'retention' => 200000000,
            'up_ceded' => 800000000,
            'jenis_reasuransi' => 'Surplus',
            'premi_reasuransi' => 2500000,
        ], $overrides));
    }

    private function storeAndLoad(object $export, string $file): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        Excel::store($export, $file, 'local');

        return IOFactory::load(Storage::disk('local')->path($file));
    }

    protected function tearDown(): void
    {
        foreach (['sheet1-produksi.xlsx', 'sheet1-single.xlsx', 'sheet1-empty.xlsx', 'sheet1-workbook.xlsx'] as $file) {
            if (Storage::disk('local')->exists($file)) {
                Storage::disk('local')->delete($file);
            }
        }

        parent::tearDown();
    }

    public function test_sheet_title_and_headings_match_template(): void
    {
        $sheet = new ProduksiSheet();

        $this->assertSame('Laporan Produksi & Premi Reasur', $sheet->title());
        $this->assertSame([
            'No Polis',
            'Nama Tertanggung',
            'Tanggal Lahir',
            'Uang Pertanggungan (UP Utama)',
            'Sendiri (Retention)',
            'UP direasuransikan (Ceded)',
            'Jenis Reasuransi',
            'Premi Reasuransi',
        ], ProduksiSheet::HEADERS);
    }

    public function test_title_header_data_and_total_positions(): void
    {
        $this->makeProduksi(['no_polis' => 'POL-001', 'premi_reasuransi' => 2500000]);
        $this->makeProduksi(['no_polis' => 'POL-002', 'premi_reasuransi' => 850000]);
        $this->makeProduksi(['no_polis' => 'POL-003', 'premi_reasuransi' => 8900000]);

        $wb = $this->storeAndLoad(new ProduksiSheet(), 'sheet1-produksi.xlsx');
        $ws = $wb->getSheetByName('Laporan Produksi & Premi Reasur');
        $this->assertNotNull($ws);

        // Title row 1 (merged), spacer row 2, headers row 3.
        $this->assertSame('Laporan Produksi & Premi Reasuransi (Borderaux Premi)', $ws->getCell('A1')->getValue());
        $this->assertSame('A1:H1', $ws->getMergeCells()['A1:H1'] ?? null);
        $this->assertSame('No Polis', $ws->getCell('A3')->getValue());
        $this->assertSame('Premi Reasuransi', $ws->getCell('H3')->getValue());

        // Data rows 4-6 in model field order.
        $this->assertSame('POL-001', $ws->getCell('A4')->getValue());
        $this->assertSame('Budi Santoso', $ws->getCell('B4')->getValue());
        $this->assertSame('1990-05-10', $ws->getCell('C4')->getValue());
        $this->assertEquals(1000000000, $ws->getCell('D4')->getValue());
        $this->assertSame('POL-003', $ws->getCell('A6')->getValue());

        // Dynamic total row 7 with live formula (never hardcoded H4:H6).
        $this->assertSame('Total', $ws->getCell('A7')->getValue());
        $this->assertSame('=SUM(H4:H6)', $ws->getCell('H7')->getValue());
        $this->assertEquals(2500000 + 850000 + 8900000, $ws->getCell('H7')->getCalculatedValue());
    }

    public function test_single_record_shifts_total_row(): void
    {
        $this->makeProduksi(['premi_reasuransi' => 1500000]);

        $ws = $this->storeAndLoad(new ProduksiSheet(), 'sheet1-single.xlsx')
            ->getSheetByName('Laporan Produksi & Premi Reasur');

        $this->assertSame('POL-001', $ws->getCell('A4')->getValue());
        $this->assertSame('Total', $ws->getCell('A5')->getValue());
        $this->assertSame('=SUM(H4:H4)', $ws->getCell('H5')->getValue());
    }

    public function test_empty_dataset_uses_safe_static_total(): void
    {
        $ws = $this->storeAndLoad(new ProduksiSheet(), 'sheet1-empty.xlsx')
            ->getSheetByName('Laporan Produksi & Premi Reasur');

        // Headers intact, total at row 4 with static 0 (no invalid range formula).
        $this->assertSame('No Polis', $ws->getCell('A3')->getValue());
        $this->assertSame('Total', $ws->getCell('A4')->getValue());
        $this->assertEquals(0, $ws->getCell('H4')->getValue());
        $this->assertFalse(is_string($ws->getCell('H4')->getValue()));
    }

    public function test_parent_workbook_still_has_three_sheets(): void
    {
        $wb = $this->storeAndLoad(new ReasuransiExport(), 'sheet1-workbook.xlsx');

        $this->assertSame([
            'Laporan Produksi & Premi Reasur',
            'Laporan Klaim Reasuransi',
            'Ringkasan Akun Keuangan',
        ], $wb->getSheetNames());
    }
}
