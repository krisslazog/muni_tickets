<?php
namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'description', 'guard_name', 'status', 'group'];

    protected $casts = [
        'status' => 'boolean',
    ];
    /**
     * Scope para filtrar permisos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope para filtrar permisos por grupo.
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
