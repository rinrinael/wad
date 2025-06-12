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
        Schema::create('konselor', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (unsignedBigInteger)
            $table->string('nama');
            $table->string('spesialisasi');
            $table->string('email')->unique();
            $table->string('password'); // Consider a longer string for hashed passwords (e.g., $table->string('password', 255);)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konselor');
    }
};