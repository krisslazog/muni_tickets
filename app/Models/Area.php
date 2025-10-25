<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model implements Auditable
{
    use HasFactory, AuditableTrait;
        protected $table = 'areas';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Ejemplo de relación con tickets (si los tendrás)
    public function tickets()
    {
        return $this->hasMany(Tktticket::class);
    }
    public function users()
    {
        return $this->hasMany(User::class, 'area_id');
    }
}
