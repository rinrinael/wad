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
        Schema::create('booking', function (Blueprint $table) { // <-- UBAH DI SINI: 'bookings'
            $table->id();
            // foreignId()->constrained() akan otomatis mencari 'users' (plural)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // foreignId()->constrained() akan otomatis mencari 'jadwals' (plural)
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');

            // $table->integer('nim');
            // references('nim')->on('mahasiswas') akan mencari 'mahasiswas' (plural)
            // $table->foreignId('nim')->constrained('mahasiswa')->onDelete('cascade');
            $table->integer('nim'); // Make sure it's 'integer' as per 'mahasiswa' table
            $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');

            $table->date('tanggal');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking'); // <-- UBAH DI SINI: 'bookings'
    }
};