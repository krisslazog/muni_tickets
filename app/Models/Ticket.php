<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

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
        return $this->belongsTo(Category::class);
    }

    // Relación con prioridad
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    // Relación con estado
    public function status()
    {
        return $this->belongsTo(Status::class);
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
}
