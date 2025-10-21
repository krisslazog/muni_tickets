<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TktCategory extends Model
{
    protected $table = 'tkt_categories';

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

    // Relación uno a muchos: tickets de esta categoría
    public function tickets()
    {
        return $this->hasMany(TktTicket::class, 'category_id');
    }
}
