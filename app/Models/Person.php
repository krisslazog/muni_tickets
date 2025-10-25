<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Person extends Model implements Auditable
{
    use HasFactory, AuditableTrait;
         protected $table = 'persons';

    protected $fillable = [
        'first_name',
        'last_name_paternal',
        'last_name_maternal',
        'document_type',
        'document_number',
        'gender',
        'birth_date',
        'phone',
        'email',
        'city',
        'address',
        'user_id',
        'status',
    ];

    // Casts para tipos específicos
    protected $casts = [
        'birth_date' => 'date:Y-m-d',
        'status' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name_paternal} {$this->last_name_maternal}"
        );
    }
}
