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
    Schema::create('klaims', function (Blueprint $table) {
        $table->id();

        $table->string('no_klaim');
        $table->string('no_polis');
        $table->string('nama_tertanggung');
        $table->string('penyebab_klaim');

        $table->date('tanggal_lahir');

        $table->decimal('total_nilai_klaim', 15, 2);
        $table->decimal('up_utama', 15, 2);
        $table->decimal('recovery', 15, 2);
        $table->decimal('up_ceded', 15, 2);

        $table->string('status_klaim');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klaims');
    }
};
