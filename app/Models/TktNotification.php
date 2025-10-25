<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TktNotification extends Model
{
    use HasFactory;
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
        return $this->belongsTo(TktTicket::class);
    }

    // Relación con persona
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
