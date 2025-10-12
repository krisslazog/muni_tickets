<?php
// routes/api.php

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Grupo de rutas para persons
Route::prefix('persons')->group(function () {

    // Buscar persona por documento
    Route::get('/search', function (Request $request) {
        try {
            // Validar parámetros
            $request->validate([
                'document_type' => 'required|string|in:DNI,CE,Pasaporte,RUC',
                'document_number' => 'required|string|min:8|max:12'
            ]);

            $person = Person::where('document_type', $request->document_type)
                ->where('document_number', $request->document_number)
                ->whereDoesntHave('user') // Solo personas sin usuario asignado
                ->first();

            if ($person) {
                return response()->json([
                    'success' => true,
                    'message' => 'Persona encontrada exitosamente',
                    'data' => [
                        'person' => $person
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ninguna persona con los datos proporcionados',
                    'data' => null
                ], 404);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors(),
                'data' => null
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => app()->environment('local') ? $e->getMessage() : 'Contacte al administrador',
                'data' => null
            ], 500);
        }
    });

    // Crear nueva persona
    Route::post('/', function (Request $request) {
        try {
            $validatedData = $request->validate([
                'document_type' => 'required|string|in:DNI,CE,Pasaporte,RUC',
                'document_number' => 'required|string|unique:persons,document_number',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|string|in:M,F,O'
            ]);

            $person = Person::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Persona creada exitosamente',
                'data' => [
                    'person' => $person
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors(),
                'data' => null
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la persona',
                'error' => app()->environment('local') ? $e->getMessage() : 'Contacte al administrador',
                'data' => null
            ], 500);
        }
    });
});
