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
        // Menambahkan kolom 'user_id' ke tabel 'konselor'
        Schema::table('konselor', function (Blueprint $table) {
            // Kolom user_id, tipe unsignedBigInteger, bisa null, diletakkan setelah kolom 'id'
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            // Menambahkan foreign key constraint:
            // 'user_id' di tabel 'konselor' mereferensi 'id' di tabel 'users'
            // Jika user di tabel 'users' dihapus, data konselor terkait juga akan dihapus (cascade)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Jika Anda ingin user_id wajib ada (tidak boleh null), hapus ->nullable()
            // Contoh: $table->unsignedBigInteger('user_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Saat rollback, hapus foreign key terlebih dahulu, lalu hapus kolomnya
        Schema::table('konselor', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Menghapus foreign key berdasarkan nama kolom
            $table->dropColumn('user_id');    // Menghapus kolom 'user_id'
        });
    }
};