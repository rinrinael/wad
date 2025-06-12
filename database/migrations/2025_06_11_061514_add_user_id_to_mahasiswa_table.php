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
        Schema::table('mahasiswa', function (Blueprint $table) {
            // Menambahkan kolom user_id setelah 'nim'
            $table->unsignedBigInteger('user_id')->nullable()->after('nim');
            // Menambahkan foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Jika Anda ingin user_id wajib ada, hapus ->nullable()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Hapus foreign key terlebih dahulu
            $table->dropColumn('user_id'); // Lalu hapus kolomnya
        });
    }
};