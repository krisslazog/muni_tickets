<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tkt_category;
use App\Models\Tkt_priority;
use App\Models\Tkt_status;
use App\Models\Tkt_attachment;
use App\Models\Tkt_comment;
use App\Models\Area;
use App\Models\Person;

class Tkt_ticket extends Model
{
    protected $table = 'tkt_tickets';

    protected $fillable = [
        'title',
        'description',
        'category_id', 
        'priority_id', 
        'status_id',   
        'area_id',
        'requester_id',
        'assignee_id',
        'resolved_at',
    ];

    // Casts para tipos específicos
    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Relaciones
    // Relación con categoría
    public function category()
    {
        return $this->belongsTo(Tkt_category::class);
    }

    // Relación con prioridad
    public function priority()
    {
        return $this->belongsTo(Tkt_priority::class);
    }

    // Relación con estado
    public function status()
    {
        return $this->belongsTo(Tkt_status::class);
    }

    // Relación con área
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Relación con quien solicita
    public function requester()
    {
        return $this->belongsTo(Person::class, 'requester_id');
    }

    // Relación con quien asigna/resuelve
    public function assignee()
    {
        return $this->belongsTo(Person::class, 'assignee_id');
    }
    // Relación con adjuntos
    public function attachments()
    {
        return $this->hasMany(Tkt_attachment::class);
    }
    // Relación con comentarios
    public function comments()
    {
        return $this->hasMany(Tkt_comment::class);
    }
}
