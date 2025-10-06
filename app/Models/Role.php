<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'tkt_roles';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relación con usuarios/personas (si lo tendrás)
    public function users()
    {
        return $this->hasMany(Person::class);
    }
}
