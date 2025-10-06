<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tkt_comment extends Model
{
    protected $table = 'tkt_categories';

    protected $fillable = [
        'ticket_id',
        'person_id',
        'content',
    ];

    // Relaciones

    // Cada comentario pertenece a un ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Cada comentario pertenece a un usuario/persona
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
