<?php

use App\Models\AppConfig;
use App\Models\AppSetting;
use Database\Seeders\BarangSeeder;
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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('nama_toko')->default('Toko Grosir Zafira');
            $table->string('alamat_toko')->nullable();
            $table->string('nomor_telepon_toko')->nullable();
            $table->string('selalu_cetak_struk')->default('false');
            $table->string('footer_struk')->default('Terima kasih telah berbelanja di toko kami, Kepuasan anda adalah tujuan kami');
            $table->timestamps();
        });
        
        AppSetting::firstOrCreate([
            'nama_toko' => 'Toko Grosir Zafira',
            'alamat_toko' => 'Ruko Terminal Parompong, Jl Kolonel Masturi, Jawa Barat',
            'nomor_telepon_toko' => '',
            'selalu_cetak_struk' => 'false',
            'footer_struk' => 'Terima kasih telah berbelanja di toko kami, Kepuasan anda adalah tujuan kami.',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
