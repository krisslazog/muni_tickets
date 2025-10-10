<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modelo User - Sistema de Tickets Municipales
 *
 * @property string $name
 * @property string $email
 * @property int|null $person_id
 * @property-read string $full_name
 * @property-read string $initials
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'person_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con Person
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Nombre completo del usuario
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->person ? $this->person->full_name : $this->name;
    }

    /**
     * Iniciales para avatar
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        if ($this->person) {
            return strtoupper(
                substr($this->person->first_name, 0, 1) .
                substr($this->person->last_name_paternal, 0, 1)
            );
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Búsqueda por nombre, email o DNI
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('person', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('document_number', 'like', "%{$search}%");
                    });
    }

    /**
     * Búsqueda solo por DNI
     * @param Builder $query
     * @param string $dni
     * @return Builder
     */
    public function scopeByDni($query, $dni)
    {
        return $query->whereHas('person', function ($q) use ($dni) {
            $q->where('document_number', $dni);
        });
    }
    /**
     * Verificar si es administrador
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Verificar si es ciudadano
     * @return bool
     */
    public function isCitizen(): bool
    {
        return $this->hasRole('citizen');
    }
}
