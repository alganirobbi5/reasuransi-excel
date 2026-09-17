<?php

namespace Database\Seeders;

use App\Models\Klaim;
use App\Models\Produksi;
use Illuminate\Database\Seeder;

class ClearDummyDataSeeder extends Seeder
{
    /**
     * Removes ONLY dummy records created by DummyDataSeeder.
     * Run with:
     *
     *   php artisan db:seed --class=ClearDummyDataSeeder
     *
     * Matching is strictly by the DUMMY- number prefixes, so real
     * application data is never touched.
     */
    public function run(): void
    {
        $produksi = Produksi::where('no_polis', 'like', DummyDataSeeder::POLIS_PREFIX.'%')->delete();
        $klaim = Klaim::where('no_klaim', 'like', DummyDataSeeder::KLAIM_PREFIX.'%')->delete();

        $this->command->info("Removed {$produksi} dummy produksi and {$klaim} dummy klaim records.");
    }
}
