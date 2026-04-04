<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Intentionally no-op. The actual midtrans_status column is added
        // in migration 2026_04_03_191137_add_midtrans_status_to_pesanan_table.
        Schema::table('pesanan', function (Blueprint $table) {
            //
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            //
        });
    }
};
