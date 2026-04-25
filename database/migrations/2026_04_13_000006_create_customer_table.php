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
        Schema::create('customer', function (Blueprint $table) {
            $table->bigIncrements('idcustomer');
            $table->string('nama', 100);
            $table->string('alamat', 255);
            $table->string('provinsi', 100);
            $table->string('kota', 100);
            $table->string('kecamatan', 100);
            $table->string('kodepos_kelurahan', 100);
            $table->binary('foto_blob')->nullable();
            $table->string('foto_blob_mime', 50)->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->string('metode_foto', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};