<?php

namespace App\Models;

use App\Models\TktCategory;
use App\Models\TktPriority;
use App\Models\TktStatus;
use App\Models\TktComment;
use App\Models\Area;
use App\Models\TktNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\User;

class TktTicket extends Model implements HasMedia, Auditable
{
    use HasFactory, InteractsWithMedia, AuditableTrait;

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
        return $this->belongsTo(TktCategory::class, 'category_id');
    }
    // Relación con prioridad
    public function priority()
    {
    return $this->belongsTo(TktPriority::class, 'priority_id');
    }

    // Relación con estado
    public function status()
    {
    return $this->belongsTo(TktStatus::class, 'status_id');
    }

    // Relación con área
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // Relación con quien solicita
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // Relación con quien asigna/resuelve
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

   // Relación con adjuntos (spatie/laravel-medialibrary)
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
    public function notifications()
    {
        return $this->hasMany(TktNotification::class, 'ticket_id');
    }
}
