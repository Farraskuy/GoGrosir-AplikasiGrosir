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
        Schema::create('tbarang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('barcode', 20)->nullable();
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_supplier');
            $table->unsignedInteger('harga_beli_grosir');
            $table->unsignedInteger('harga_jual_grosir');
            $table->string('satuan_grosir', 100);
            $table->string('with_eceran', 2)->nullable();
            $table->string('isi_barang', 100)->default(0);
            $table->unsignedInteger('harga_beli_ecer')->default(0);
            $table->unsignedInteger('harga_jual_ecer')->default(0);
            $table->string('satuan_ecer', 100)->nullable();
            $table->foreign('id_kategori')->references('id')->on('tkategori')->restrictOnDelete();
            $table->foreign('id_supplier')->references('id')->on('tsupplier')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbarang');
    }
};
