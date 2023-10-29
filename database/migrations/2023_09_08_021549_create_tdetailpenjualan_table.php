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
        Schema::create('tdetailpenjualan', function (Blueprint $table) {
            $table->string('no_trans', 22);
            $table->unsignedBigInteger('id_barang');
            $table->unsignedInteger('jumlah_beli');
            $table->unsignedInteger('harga_beli');
            $table->unsignedInteger('harga_jual');
            $table->unsignedInteger('potongan');
            $table->unsignedInteger('subtotal');
            $table->foreign('id_barang')->references('id')->on('tbarang')->restrictOnDelete();
            $table->foreign('no_trans')->references('no_trans')->on('tpenjualan')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tdetailpenjualan');
    }
};
