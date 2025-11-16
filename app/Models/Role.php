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
     * Sobrescribir el scope base para incluir solo roles activos.
     */
    protected static function boot()
    {
        parent::boot();

        // Filtrar automáticamente solo roles activos en todas las consultas
        static::addGlobalScope('active', function ($query) {
            $query->where('status', true);
        });
    }
}
