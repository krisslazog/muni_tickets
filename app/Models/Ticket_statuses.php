<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenVenta extends Model
{
    protected $table = 'persons';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    // Casts para tipos específicos
    protected $casts = [
        'status' => 'boolean',
    ];

    // Relaciones

    // Relación uno a muchos: tickets con esta prioridad
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
