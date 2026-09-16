<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('produksis', function (Blueprint $table) {
        $table->id();

        $table->string('no_polis');
        $table->string('nama_tertanggung');
        $table->date('tanggal_lahir');

        $table->decimal('up_utama', 15, 2);
        $table->decimal('retention', 15, 2);
        $table->decimal('up_ceded', 15, 2);

        $table->string('jenis_reasuransi');
        $table->decimal('premi_reasuransi', 15, 2);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksis');
    }
};
