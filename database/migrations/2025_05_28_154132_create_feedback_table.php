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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->integer('nim'); // Ensure this matches the 'nim' type in 'mahasiswa'
            $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');
            $table->unsignedBigInteger('konselor_id'); // Changed to unsignedBigInteger to match 'id' of 'konselor'
            $table->foreign('konselor_id')->references('id')->on('konselor')->onDelete('cascade');
            $table->text('komentar'); // Changed to text for potentially longer comments
            $table->integer('rating'); // Consider adding constraints like ->unsigned() and ->min(1)->max(5)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};