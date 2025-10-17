<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
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
        return $this->hasMany(Tkt_ticket::class);
    }
}
