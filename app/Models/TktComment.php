<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TktComment extends Model implements Auditable
{
    use HasFactory, AuditableTrait;
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
        return $this->belongsTo(TktTicket::class);
    }

    // Cada comentario pertenece a un usuario/persona
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
