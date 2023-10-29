<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id'
    ];

    /**
     * Get the permission that owns the RolePermission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }


}
