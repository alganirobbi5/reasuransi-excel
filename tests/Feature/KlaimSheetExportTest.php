<?php

namespace Tests\Feature;

use App\Exports\KlaimSheet;
use App\Exports\ReasuransiExport;
use App\Models\Klaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class KlaimSheetExportTest extends TestCase
{
    use RefreshDatabase;

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
            'status_klaim' => 'Approved/Siap Dibayarkan',
        ], $overrides));
    }

    private function storeAndLoad(object $export, string $file): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        Excel::store($export, $file, 'local');

        return IOFactory::load(Storage::disk('local')->path($file));
    }

    protected function tearDown(): void
    {
        foreach (['sheet2-klaim.xlsx', 'sheet2-multi.xlsx', 'sheet2-empty.xlsx', 'sheet2-workbook.xlsx'] as $file) {
            if (Storage::disk('local')->exists($file)) {
                Storage::disk('local')->delete($file);
            }
        }

        parent::tearDown();
    }

    public function test_sheet_title_is_clean_without_trailing_whitespace(): void
    {
        $this->assertSame('Laporan Klaim Reasuransi', (new KlaimSheet())->title());
    }

    public function test_title_and_headers_match_template_verbatim(): void
    {
        $this->makeKlaim();

        $ws = $this->storeAndLoad(new KlaimSheet(), 'sheet2-klaim.xlsx')
            ->getSheetByName('Laporan Klaim Reasuransi');
        $this->assertNotNull($ws);

        $this->assertSame('Laporan Klaim Reasuransi (Borderaux Klaim)', $ws->getCell('A1')->getValue());
        $this->assertSame('A1:J1', $ws->getMergeCells()['A1:J1'] ?? null);

        $expected = [
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
        $this->assertSame($expected, KlaimSheet::HEADERS);
        foreach (array_values($expected) as $i => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $this->assertSame($header, $ws->getCell($col.'3')->getValue());
        }
    }

    public function test_single_klaim_mapping_starts_at_row_4(): void
    {
        $this->makeKlaim();

        $ws = $this->storeAndLoad(new KlaimSheet(), 'sheet2-klaim.xlsx')
            ->getSheetByName('Laporan Klaim Reasuransi');

        $this->assertSame('KLM-001', $ws->getCell('A4')->getValue());
        $this->assertSame('POL-001', $ws->getCell('B4')->getValue());
        $this->assertSame('Siti Aminah', $ws->getCell('C4')->getValue());
        $this->assertSame('Sakit', $ws->getCell('D4')->getValue());
        $this->assertSame('1985-03-20', $ws->getCell('E4')->getValue());
        $this->assertEquals(1500000000, $ws->getCell('F4')->getValue());
        $this->assertEquals(1000000000, $ws->getCell('G4')->getValue());
        $this->assertEquals(700000000, $ws->getCell('H4')->getValue());
        $this->assertEquals(800000000, $ws->getCell('I4')->getValue());
        $this->assertSame('Approved/Siap Dibayarkan', $ws->getCell('J4')->getValue());

        // No total row by template design: row 5 must be empty.
        $this->assertNull($ws->getCell('A5')->getValue());
        $this->assertNull($ws->getCell('J5')->getValue());
    }

    public function test_multiple_klaim_rows_are_ordered(): void
    {
        $this->makeKlaim(['no_klaim' => 'KLM-001', 'status_klaim' => 'Approved/Siap Dibayarkan']);
        $this->makeKlaim(['no_klaim' => 'KLM-002', 'status_klaim' => 'Dalam Investigasi']);

        $ws = $this->storeAndLoad(new KlaimSheet(), 'sheet2-multi.xlsx')
            ->getSheetByName('Laporan Klaim Reasuransi');

        $this->assertSame('KLM-001', $ws->getCell('A4')->getValue());
        $this->assertSame('KLM-002', $ws->getCell('A5')->getValue());
        $this->assertSame('Dalam Investigasi', $ws->getCell('J5')->getValue());
    }

    public function test_empty_dataset_keeps_title_and_headers_only(): void
    {
        $ws = $this->storeAndLoad(new KlaimSheet(), 'sheet2-empty.xlsx')
            ->getSheetByName('Laporan Klaim Reasuransi');
        $this->assertNotNull($ws);

        $this->assertSame('Laporan Klaim Reasuransi (Borderaux Klaim)', $ws->getCell('A1')->getValue());
        $this->assertSame('No Klaim', $ws->getCell('A3')->getValue());
        $this->assertSame('Status Klaim', $ws->getCell('J3')->getValue());
        $this->assertNull($ws->getCell('A4')->getValue());
        $this->assertNull($ws->getCell('J4')->getValue());
    }

    public function test_parent_workbook_still_has_three_sheets(): void
    {
        $wb = $this->storeAndLoad(new ReasuransiExport(), 'sheet2-workbook.xlsx');

        $this->assertSame([
            'Laporan Produksi & Premi Reasur',
            'Laporan Klaim Reasuransi',
            'Ringkasan Akun Keuangan',
        ], $wb->getSheetNames());
    }
}
