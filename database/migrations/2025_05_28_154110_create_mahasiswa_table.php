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
    Schema::create('mahasiswa', function (Blueprint $table) { // <-- UBAH KE 'mahasiswas'
        $table->bigInteger('nim')->primary();
        $table->unsignedBigInteger('jadwal_id')->nullable();
        $table->foreign('jadwal_id')->references('id')->on('jadwal')->onDelete('set null'); // <-- UBAH KE 'jadwals'
        $table->date('tanggal'); // Pastikan ini tanggal lahir atau apa, jika bukan, sesuaikan nama
        $table->string('nama');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('mahasiswa'); // <-- UBAH KE 'mahasiswas'
}
};