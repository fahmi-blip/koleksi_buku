<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor')->index();
            $table->string('name');
            $table->text('layanan')->nullable(); // json or comma list
            $table->enum('status', ['menunggu','dipanggil','selesai','terlambat'])->default('menunggu')->index();
            $table->timestamp('called_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('antrians');
    }
};
