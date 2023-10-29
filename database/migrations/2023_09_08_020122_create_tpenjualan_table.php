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
        Schema::create('tpenjualan', function (Blueprint $table) {
            $table->string('no_trans', 22)->primary();
            $table->unsignedBigInteger('id_petugas');
            $table->unsignedInteger('total_bayar');
            $table->unsignedInteger('total_potongan');
            $table->unsignedInteger('bayar');
            $table->unsignedInteger('kembalian');
            $table->timestamps();
            $table->foreign('id_petugas')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpenjualan');
    }
};
