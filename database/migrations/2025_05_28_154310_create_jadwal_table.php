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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('konselor_id'); 
            $table->foreign('konselor_id')->references('id')->on('konselor')->onDelete('cascade');
            $table->date('hari'); 
            $table->time('waktu');
            $table->string('status');
            $table->timestamps();
        });
  
  }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};