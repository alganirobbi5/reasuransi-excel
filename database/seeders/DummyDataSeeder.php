<?php

namespace Database\Seeders;

use App\Models\Klaim;
use App\Models\Produksi;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public const POLIS_PREFIX = 'DUMMY-POL-';

    public const KLAIM_PREFIX = 'DUMMY-CLM-';

    /**
     * Explicit seeder for testing/demo only. Run with:
     *
     *   php artisan db:seed --class=DummyDataSeeder
     *
     * NOT called from DatabaseSeeder, so a plain `db:seed` never
     * inserts dummy data. Re-runnable: records are matched by their
     * unique dummy numbers via updateOrCreate().
     */
    public function run(): void
    {
        foreach ($this->produksiRows() as $row) {
            Produksi::updateOrCreate(
                ['no_polis' => $row['no_polis']],
                $row
            );
        }

        foreach ($this->klaimRows() as $row) {
            Klaim::updateOrCreate(
                ['no_klaim' => $row['no_klaim']],
                $row
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function produksiRows(): array
    {
        return [
            $this->produksi('DUMMY-POL-001', 'Andi Pratama', '1990-05-12', 500000000, 100000000, 400000000, 'Quota Share', 1500000),
            $this->produksi('DUMMY-POL-002', 'Budi Santoso', '1987-09-21', 750000000, 150000000, 600000000, 'Surplus', 2250000),
            $this->produksi('DUMMY-POL-003', 'Citra Lestari', '1995-02-14', 1000000000, 200000000, 800000000, 'Fac/Surplus', 3500000),
            $this->produksi('DUMMY-POL-004', 'Dedi Wijaya', '1982-11-03', 1250000000, 250000000, 1000000000, 'Surplus', 4200000),
            $this->produksi('DUMMY-POL-005', 'Eka Permata', '1992-07-30', 600000000, 120000000, 480000000, 'Quota Share', 1800000),
            $this->produksi('DUMMY-POL-006', 'Fajar Nugroho', '1978-03-17', 900000000, 180000000, 720000000, 'Surplus', 2750000),
            $this->produksi('DUMMY-POL-007', 'Gita Puspita', '1998-12-05', 1500000000, 300000000, 1200000000, 'Fac/Surplus', 5100000),
            $this->produksi('DUMMY-POL-008', 'Hendra Gunawan', '1975-06-28', 450000000, 90000000, 360000000, 'Quota Share', 980000),
            $this->produksi('DUMMY-POL-009', 'Irma Sari', '1989-10-11', 1100000000, 220000000, 880000000, 'Surplus', 3300000),
            $this->produksi('DUMMY-POL-010', 'Joko Susilo', '1993-04-22', 800000000, 160000000, 640000000, 'Quota Share', 2050000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function produksi(
        string $noPolis,
        string $nama,
        string $lahir,
        int $upUtama,
        int $retention,
        int $upCeded,
        string $jenis,
        int $premi
    ): array {
        return [
            'no_polis' => $noPolis,
            'nama_tertanggung' => $nama,
            'tanggal_lahir' => $lahir,
            'up_utama' => $upUtama,
            'retention' => $retention,
            'up_ceded' => $upCeded,
            'jenis_reasuransi' => $jenis,
            'premi_reasuransi' => $premi,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function klaimRows(): array
    {
        return [
            $this->klaim('DUMMY-CLM-001', 'DUMMY-POL-001', 'Andi Pratama', 'Meninggal Dunia', '1990-05-12', 300000000, 500000000, 120000000, 400000000, 'Approved'),
            $this->klaim('DUMMY-CLM-002', 'DUMMY-POL-002', 'Budi Santoso', 'Meninggal Dunia', '1987-09-21', 500000000, 750000000, 250000000, 600000000, 'Dalam Investigasi'),
            $this->klaim('DUMMY-CLM-003', 'DUMMY-POL-003', 'Citra Lestari', 'Klaim Kesehatan', '1995-02-14', 200000000, 1000000000, 160000000, 800000000, 'Siap Dibayarkan'),
            $this->klaim('DUMMY-CLM-004', 'DUMMY-POL-004', 'Dedi Wijaya', 'Kecelakaan', '1982-11-03', 750000000, 1250000000, 500000000, 1000000000, 'Dalam Investigasi'),
            $this->klaim('DUMMY-CLM-005', 'DUMMY-POL-005', 'Eka Permata', 'Klaim Kesehatan', '1992-07-30', 150000000, 600000000, 90000000, 480000000, 'Approved'),
            $this->klaim('DUMMY-CLM-006', 'DUMMY-POL-006', 'Fajar Nugroho', 'Meninggal Dunia', '1978-03-17', 900000000, 900000000, 720000000, 720000000, 'Siap Dibayarkan'),
            $this->klaim('DUMMY-CLM-007', 'DUMMY-POL-007', 'Gita Puspita', 'Kecelakaan', '1998-12-05', 400000000, 1500000000, 320000000, 1200000000, 'Dalam Investigasi'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function klaim(
        string $noKlaim,
        string $noPolis,
        string $nama,
        string $penyebab,
        string $lahir,
        int $totalNilai,
        int $upUtama,
        int $recovery,
        int $upCeded,
        string $status
    ): array {
        return [
            'no_klaim' => $noKlaim,
            'no_polis' => $noPolis,
            'nama_tertanggung' => $nama,
            'penyebab_klaim' => $penyebab,
            'tanggal_lahir' => $lahir,
            'total_nilai_klaim' => $totalNilai,
            'up_utama' => $upUtama,
            'recovery' => $recovery,
            'up_ceded' => $upCeded,
            'status_klaim' => $status,
        ];
    }
}
