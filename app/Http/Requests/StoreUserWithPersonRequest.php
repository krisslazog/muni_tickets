<?php
// app/Http/Requests/StoreUserWithPersonRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Person;
use App\Models\User;

class StoreUserWithPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // o tu lógica de autorización
    }

    public function rules(): array
    {
        return [
            // Datos de la persona
            'id' => 'nullable|exists:persons,id',
            'document_type' => 'required|string|in:DNI,CE,Pasaporte,RUC',
            'document_number' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $exists = Person::where('document_type', $this->document_type)
                                  ->where('document_number', $value)
                                  ->when($this->id, function ($query, $personId) {
                                      return $query->where('id', '!=', $personId);
                                  })
                                  ->exists();

                    if ($exists) {
                        $fail('Ya existe una persona registrada con este documento.');
                    }
                }
            ],
            'first_name' => 'required|string|max:100',
            'last_name_paternal' => 'required|string|max:100',
            'last_name_maternal' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
    function ($attribute, $value, $fail) {
        if (!$value) {
            return;
        }

        $personId = $this->input('id');        // <-- aquí tu campo persona
        $userId   = $this->input('user_id');   // <-- aquí tu campo usuario

        $existsPerson = Person::where('email', $value)
            ->when($personId, fn($q) => $q->where('id', '!=', $personId))
            ->exists();

        $existsUser = User::where('email', $value)
            ->when($userId, fn($q) => $q->where('id', '!=', $userId))
            ->exists();

        if ($existsPerson || $existsUser) {
            $fail('El correo electrónico ya está siendo utilizado.');
        }
    },
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:M,F,O',

            // Datos del usuario
            'user_id' => 'nullable|exists:users,id',
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable',

        ];
    }

    public function messages(): array
    {
        return [
            // ✅ MENSAJES COMPLETAMENTE PERSONALIZADOS

            // Documento
            'document_type.required' => '🔸 Por favor, selecciona un tipo de documento.',
            'document_type.in' => '❌ El tipo de documento seleccionado no es válido.',
            'document_number.required' => '🔸 El número de documento es obligatorio.',
            'document_number.max' => '⚠️ El número de documento es demasiado largo (máximo 20 caracteres).',

            // Nombres
            'first_name.required' => '🔸 Los nombres son obligatorios.',
            'first_name.max' => '⚠️ Los nombres son demasiado largos (máximo 100 caracteres).',
            'last_name_paternal.required' => '🔸 El apellido paterno es obligatorio.',
            'last_name_paternal.max' => '⚠️ El apellido paterno es demasiado largo (máximo 100 caracteres).',
            'last_name_maternal.required' => '🔸 El apellido materno es obligatorio.',
            'last_name_maternal.max' => '⚠️ El apellido materno es demasiado largo (máximo 100 caracteres).',

            // Email
            'email.required' => '🔸 El correo electrónico es obligatorio.',
            'email.email' => '📧 Por favor, ingresa un correo electrónico válido.',
            'email.max' => '⚠️ El correo electrónico es demasiado largo.',

            // Contacto
            'phone.max' => '📱 El número de teléfono es demasiado largo.',
            'address.max' => '🏠 La dirección es demasiado larga (máximo 500 caracteres).',
            'birth_date.date' => '📅 La fecha de nacimiento no es válida.',
            'birth_date.before' => '📅 La fecha de nacimiento debe ser anterior a hoy.',
            'gender.in' => '👤 Por favor, selecciona un género válido.',

            // Usuario

            'name.required' => '🔸 El nombre completo del usuario es obligatorio.',
            'name.max' => '⚠️ El nombre del usuario es demasiado largo.',
            'password.required' => '🔒 La contraseña es obligatoria.',
            'password.min' => '🔒 La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => '❌ Las contraseñas no coinciden. Verifica que ambas sean iguales.',
            'password_confirmation.required' => '🔒 Debes confirmar la contraseña.',

        ];
    }

    public function attributes(): array
    {
        return [
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento',
            'first_name' => 'nombres',
            'last_name_paternal' => 'apellido paterno',
            'last_name_maternal' => 'apellido materno',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'address' => 'dirección',
            'birth_date' => 'fecha de nacimiento',
            'gender' => 'género',
            'user_id' => 'usuario',
            'name' => 'nombre completo',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
        ];
    }
}
