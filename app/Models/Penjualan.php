<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'tpenjualan';
    protected $primaryKey = 'no_trans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'no_trans',
        'id_petugas',
        'total_bayar',
        'total_potongan',
        'bayar',
        'kembalian'
    ];


    /**
     * Get all of the comments for the Penjualan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'no_trans', 'no_trans');
    }

    /**
     * Get the user that owns the Penjualan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id');
    }
}
