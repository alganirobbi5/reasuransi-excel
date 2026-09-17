<?php

namespace Tests\Feature;

use App\Exports\ReasuransiExport;
use App\Exports\RingkasanKeuanganSheet;
use App\Models\Klaim;
use App\Models\Produksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class RingkasanKeuanganSheetTest extends TestCase
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

    private function makeKlaim(array $overrides = []): Klaim
    {
        return Klaim::create(array_merge([
            'no_klaim' => 'KLM-001',
            'no_polis' => 'POL-001',
            'nama_tertanggung' => 'Siti Aminah',
            'penyebab_klaim' => 'Sakit',
            'tanggal_lahir' => '1985-03-20',
            'total_nilai_klaim' => 1500000000,
            'up_utama' => 1000000000,
            'recovery' => 700000000,
            'up_ceded' => 800000000,
            'status_klaim' => 'Dalam Investigasi',
        ], $overrides));
    }

    private function storeAndLoad(object $export, string $file): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        Excel::store($export, $file, 'local');

        return IOFactory::load(Storage::disk('local')->path($file));
    }

    protected function tearDown(): void
    {
        foreach (['sheet3-layout.xlsx', 'sheet3-zero.xlsx', 'sheet3-multi.xlsx', 'sheet3-empty.xlsx', 'sheet3-workbook.xlsx'] as $file) {
            if (Storage::disk('local')->exists($file)) {
                Storage::disk('local')->delete($file);
            }
        }

        parent::tearDown();
    }

    private function sheet3(string $file): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $ws = $this->storeAndLoad(new RingkasanKeuanganSheet(), $file)
            ->getSheetByName('Ringkasan Akun Keuangan');
        $this->assertNotNull($ws);

        return $ws;
    }

    public function test_sheet_3_has_correct_name(): void
    {
        $this->assertSame('Ringkasan Akun Keuangan', (new RingkasanKeuanganSheet())->title());
    }

    public function test_sheet_3_has_correct_layout_and_merges(): void
    {
        $ws = $this->sheet3('sheet3-layout.xlsx');
        $merges = array_keys($ws->getMergeCells());

        foreach (['A1:O1', 'A4:A11', 'B4:C4', 'B5:C6', 'B7:C10', 'B11:C11', 'E12:F12'] as $expected) {
            $this->assertContains($expected, $merges, "Missing merge {$expected}");
        }
        $this->assertSame('Ringkasan Akun Keuangan', $ws->getCell('A1')->getValue());
        $this->assertSame('Logic/kalkulasi', $ws->getCell('A4')->getValue());
    }

    public function test_sheet_3_has_exact_labels_verbatim(): void
    {
        $ws = $this->sheet3('sheet3-layout.xlsx');

        $this->assertSame('Total Premi', $ws->getCell('B4')->getValue());
        $this->assertSame('Reinsurance Commision', $ws->getCell('E4')->getValue());
        $this->assertSame('Total Premi Reasuransi/Netto', $ws->getCell('H4')->getValue());
        $this->assertSame('Recovery', $ws->getCell('K4')->getValue());
        $this->assertSame('Saldo Akhir', $ws->getCell('N4')->getValue());

        $this->assertSame('Premi Reasuransi Gross', $ws->getCell('B7')->getValue());
        $this->assertSame('Dikurangi Komisi reasuransi', $ws->getCell('E7')->getValue());
        $this->assertSame('Premi Netto', $ws->getCell('H7')->getValue());
        // Typo + trailing whitespace preserved from template.
        $this->assertSame('Dikurangi Total Klaim Reasunasi ', $ws->getCell('K7')->getValue());
        $this->assertSame(
            'Perusahaan Reasuransi harus membayar jumlah ini kepada Cedant karena nilai klaim lebih besar dari premi)',
            $ws->getCell('N7')->getValue()
        );
    }

    public function test_sheet_3_has_dynamic_sheet_1_reference_for_zero_production(): void
    {
        $ws = $this->sheet3('sheet3-zero.xlsx');

        $this->assertSame("='Laporan Produksi & Premi Reasur'!H4", $ws->getCell('B11')->getValue());
    }

    public function test_sheet_3_has_dynamic_sheet_1_reference_for_multiple_productions(): void
    {
        $this->makeProduksi(['no_polis' => 'POL-001']);
        $this->makeProduksi(['no_polis' => 'POL-002']);
        $this->makeProduksi(['no_polis' => 'POL-003']);

        $ws = $this->sheet3('sheet3-multi.xlsx');

        // Sheet 1 total sits at row 4 + 3 records = row 7.
        $this->assertSame("='Laporan Produksi & Premi Reasur'!H7", $ws->getCell('B11')->getValue());
    }

    public function test_formula_cells_are_formulas(): void
    {
        $ws = $this->sheet3('sheet3-layout.xlsx');

        foreach (['B5' => '=B11', 'E5' => '=E12', 'H5' => '=H11', 'K5' => '=K11', 'N5' => '=N11'] as $cell => $formula) {
            $this->assertSame($formula, $ws->getCell($cell)->getValue(), "{$cell} must hold {$formula}");
        }
        $this->assertSame('=B11*E11', $ws->getCell('E12')->getValue());
        $this->assertSame('=B11-E12', $ws->getCell('H11')->getValue());
        $this->assertSame('=K11-H11', $ws->getCell('N11')->getValue());
        $this->assertStringStartsWith('=', (string) $ws->getCell('B11')->getValue());
    }

    public function test_commission_rate_is_10_percent(): void
    {
        $ws = $this->sheet3('sheet3-layout.xlsx');

        $this->assertEquals(0.1, $ws->getCell('E11')->getValue());
    }

    public function test_k11_holds_total_recovery_from_database(): void
    {
        $this->makeKlaim(['recovery' => 700000000]);
        $this->makeKlaim(['no_klaim' => 'KLM-002', 'recovery' => 1250000000]);

        $ws = $this->sheet3('sheet3-layout.xlsx');

        $this->assertEquals(700000000 + 1250000000, $ws->getCell('K11')->getValue());
        $this->assertFalse(is_string($ws->getCell('K11')->getValue()));
        // N11 keeps its formula against the live K11 value.
        $this->assertSame('=K11-H11', $ws->getCell('N11')->getValue());
    }

    public function test_k11_is_zero_without_klaim(): void
    {
        $ws = $this->sheet3('sheet3-empty.xlsx');

        $this->assertEquals(0, $ws->getCell('K11')->getValue());
        $this->assertFalse(is_string($ws->getCell('K11')->getValue()));
    }

    public function test_workbook_still_contains_three_sheets(): void
    {
        $wb = $this->storeAndLoad(new ReasuransiExport(), 'sheet3-workbook.xlsx');

        $this->assertSame([
            'Laporan Produksi & Premi Reasur',
            'Laporan Klaim Reasuransi',
            'Ringkasan Akun Keuangan',
        ], $wb->getSheetNames());
    }
}
