<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/YYYY_MM_DD_HHMMSS_create_jadwal_table.php
public function up(): void
{
    Schema::create('jadwal', function (Blueprint $table) { // <-- UBAH KE 'jadwals'
        $table->id();
        // $table->unsignedBigInteger('konselor_id');
        $table->foreignId('konselor_id')->constrained('konselor')->onDelete('cascade'); // <-- UBAH KE 'konselors'
        $table->date('hari');
        $table->time('waktu');
        $table->string('status');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('jadwal'); // <-- UBAH KE 'jadwals'
}
};  