<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TktStatus extends Model
{
    protected $table = 'tkt_statuses';

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
        return $this->hasMany(TktTicket::class, 'status_id');
    }
}
