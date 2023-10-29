<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'tbarang';
    protected $fillable = [
        'barcode',
        'nama',
        'id_kategori',
        'id_supplier',
        'harga_beli_grosir',
        'harga_jual_grosir',
        'satuan_grosir',
        'with_eceran',
        'harga_beli_ecer',
        'harga_jual_ecer',
        'satuan_ecer',
        'isi_barang',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }
}
