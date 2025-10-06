<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tkt_priority extends Model
{
    protected $table = 'tkt_priorities';

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
        return $this->hasMany(Ticket::class, 'priority_id');
    }
}
