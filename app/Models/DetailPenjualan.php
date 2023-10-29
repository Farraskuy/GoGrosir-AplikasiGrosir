<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    use HasFactory;
    
    protected $table = 'tdetailpenjualan';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'no_trans',
        'id_barang',
        'satuan_beli',
        'harga_beli',
        'harga_jual',
        'jumlah_beli',
        'potongan',
        'subtotal'
    ];

    /**
     * Get the user that owns the DetailPenjualan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }
}
