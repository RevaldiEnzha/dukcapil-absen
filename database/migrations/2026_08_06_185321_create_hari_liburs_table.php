<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_liburs', function (Blueprint $table) {
            $table->id();
            // Unique agar 1 tanggal tidak bisa diinput libur 2 kali
            $table->date('tanggal')->unique(); 
            $table->string('jenis_libur'); // Akan berisi: "Tanggal Merah" atau "Cuti Bersama"
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_liburs');
    }
};