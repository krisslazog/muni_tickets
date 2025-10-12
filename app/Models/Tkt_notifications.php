<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tkt_notificarion extends Model
{
    protected $table = 'tkt_notifications';

    protected $fillable = [
        'ticket_id',
        'person_id',
        'message',
        'is_read',
        'created_at',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relación con ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relación con persona
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
