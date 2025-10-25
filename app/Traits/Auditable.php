<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * El "método de arranque" del trait.
     */
    protected static function bootAuditable()
    {
        // Evento "creating": Se dispara ANTES de crear un registro
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        // Evento "updating": Se dispara ANTES de actualizar un registro
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}