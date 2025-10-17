<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'first_name',
        'last_name_paternal',
        'last_name_maternal',
        'document_type',
        'document_number',
        'gender',
        'birth_date',
        'phone',
        'email',
        'address',
        'status'
    ];

    // Casts para tipos específicos
    protected $casts = [
        'birth_date' => 'date:Y-m-d',
        'status' => 'boolean'
    ];

    // Relaciones

    // Relación uno a uno con User
    public function user()
    {
        return $this->hasMany(User::class);
    }

    // Relación uno a muchos: tickets que solicitó
    public function requestedTickets()
    {
        return $this->hasMany(Tkt_ticket::class, 'requester_id');
    }

    // Relación uno a muchos: tickets que tiene asignados
    public function assignedTickets()
    {
        return $this->hasMany(Tkt_ticket::class, 'assignee_id');
    }

    // Relación comentarios realizados
    public function comments()
    {
        return $this->hasMany(Tkt_comment::class, 'person_id');
    }

    // Relación notificaciones
    public function notifications()
    {
        return $this->hasMany(Tkt_notification::class, 'person_id');
    }
}
