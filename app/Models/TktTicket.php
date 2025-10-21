<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TktCategory;
use App\Models\TktPriority;
use App\Models\TktStatus;
use App\Models\TktComment;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Area;
use App\Models\Person;

class TktTicket extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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
        return $this->belongsTo(TktCategory::class);
    }

    // Relación con prioridad
    public function priority()
    {
        return $this->belongsTo(TktPriority::class);
    }

    // Relación con estado
    public function status()
    {
        return $this->belongsTo(TktStatus::class);
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
    // Relación con attachments
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            // Opcional: define qué tipos de archivo aceptas
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }
    // Relación con comentarios
    public function comments()
    {
        // --- ¡CORRECCIÓN FUTURA AQUÍ! ---
        return $this->hasMany(TktComment::class); // <-- CORREGIDO (sin guion bajo)
    }
}
