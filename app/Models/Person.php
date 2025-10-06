<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';

    protected $fillable = [
        'name',
        'paternal_surname',
        'maternal_surname',
        'email',
        'birth_date',
        'phone',
        'status',
        'gender',
    ];

    // Casts para tipos específicos
    protected $casts = [
        'status' => 'boolean',
        'birth_date' => 'date',
    ];

    // Relaciones

    // Relación uno a uno con User
    public function user()
    {
        return $this->hasOne(User::class);
    }

    // Relación uno a muchos: tickets que solicitó
    public function requestedTickets()
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    // Relación uno a muchos: tickets que tiene asignados
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    // Relación comentarios realizados
    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'person_id');
    }

    // Relación notificaciones
    public function notifications()
    {
        return $this->hasMany(TicketNotification::class, 'person_id');
    }
}
