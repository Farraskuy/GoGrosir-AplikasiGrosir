<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'tsupplier';

    protected $fillable = [
        'nama',
        'nomor_telepon',
        'alamat',
    ];
}
