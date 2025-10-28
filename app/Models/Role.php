<?php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'description', 'guard_name', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope para filtrar roles activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
