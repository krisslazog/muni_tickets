<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $personId = $this->route('person')->id;
        
        return [
            'first_name' => 'required|string|max:100',
            'last_name_paternal' => 'required|string|max:100',
            'last_name_maternal' => 'nullable|string|max:100',
            'document_type' => 'required|string|in:DNI,CE,PASSPORT,RUC',
            'document_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('persons', 'document_number')->ignore($personId)
            ],
            'gender' => 'required|string|in:M,F',
            'birth_date' => 'required|date|before:today',
            'phone' => 'nullable|string|max:15',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('persons', 'email')->ignore($personId)
            ],
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'status' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre no puede exceder 100 caracteres.',
            'last_name_paternal.required' => 'El apellido paterno es obligatorio.',
            'last_name_paternal.max' => 'El apellido paterno no puede exceder 100 caracteres.',
            'last_name_maternal.max' => 'El apellido materno no puede exceder 100 caracteres.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento debe ser DNI, CE, PASSPORT o RUC.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
            'document_number.max' => 'El número de documento no puede exceder 20 caracteres.',
            'gender.required' => 'El género es obligatorio.',
            'gender.in' => 'El género debe ser M (Masculino) o F (Femenino).',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'phone.max' => 'El teléfono no puede exceder 15 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Este email ya está registrado.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'city.max' => 'La ciudad no puede exceder 100 caracteres.',
            'address.max' => 'La dirección no puede exceder 255 caracteres.',
        ];
    }
}