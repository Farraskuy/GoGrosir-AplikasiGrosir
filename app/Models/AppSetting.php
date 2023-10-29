<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = [
        'nama_toko',
        'alamat_toko',
        'nomor_telepon_toko',
        'selalu_cetak_struk',
        'footer_struk',
    ];
}
