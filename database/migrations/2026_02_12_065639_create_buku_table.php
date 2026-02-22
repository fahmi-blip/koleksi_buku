<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id('idbuku');
            $table->foreignId('idkategori')->constrained('kategori','idkategori')->onDelete('restrict');
            $table->string('kode')->unique();
            $table->string('judul');
            $table->string('pengarang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
