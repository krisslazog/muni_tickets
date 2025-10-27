<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TktPriority extends Model
{
    use HasFactory, Auditable;
    protected $table = 'tkt_priorities';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    // Casts para tipos específicos
    protected $casts = [
        'status' => 'boolean',
    ];

    // Relaciones

    // Relación uno a muchos: tickets con esta prioridad
    public function tickets()
    {
        return $this->hasMany(TktTicket::class, 'priority_id');
    }
    
    public function createdBy()
    {

    return $this->belongsTo(User::class, 'created_by_id'); 
    }

    public function updatedBy()
    {
   
    return $this->belongsTo(User::class, 'updated_by_id');
    }
}
