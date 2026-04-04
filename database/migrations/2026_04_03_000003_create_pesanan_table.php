<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('idpesanan');
            $table->string('order_code', 30)->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('idvendor');
            $table->string('nama', 255);
            $table->timestamp('timestamp')->useCurrent();
            $table->integer('total')->default(0);
            $table->string('metode_bayar', 30);
            $table->smallInteger('status_bayar')->default(0);
            $table->string('payment_reference', 100)->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('idvendor')
                ->references('idvendor')
                ->on('vendor')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};