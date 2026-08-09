<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom deleted_at di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes(); 
        });

        // Tambahkan kolom deleted_at di tabel pegawais
        Schema::table('pegawais', function (Blueprint $table) {
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};