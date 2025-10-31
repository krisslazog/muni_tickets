<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TktIssue; 
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule; // Necesario para el método update

class IssueController extends Controller
{

    /**
     * Muestra el listado de incidencias.
     */
    public function index(Request $request)
    {
        $issues = TktIssue::all();

        return Inertia::render('Tickets/Issues/Index',
            [
                'issues' => $issues,
            ]);
    }
    
    /**
     * Muestra el formulario para crear una nueva incidencia.
     */
    public function create()
    {
        return Inertia::render('Tickets/Issues/Create');
    }

    /**
     * Crea una nueva incidencia.
     */
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'name' => 'required|string|max:255|unique:tkt_issues',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        
        // Crear la incidencia en la base de datos
        TktIssue::create([
            'name' => $request->name,
            'description' => $request->description,
            // Asigna 'true' solo si no se envía o si la validación lo requiere
            'status' => $request->status ?? true, 
        ]);
        
        // Redirigir al listado de incidencias
        return redirect()->route('tickets.issues.index')
                         ->with('success', 'Incidencia creada correctamente.');
    }

    /**
     * Muestra el formulario para editar una incidencia.
     */
    public function edit($id)
    {
        // Buscar incidencia por id
        $issue = TktIssue::findOrFail($id);
        
        // Retornar vista con la incidencia
        return Inertia::render('Tickets/Issues/Edit', [ 
            'issue' => $issue,
        ]);
    }
    
    /**
     * Actualiza una incidencia existente.
     */
    public function update(Request $request, $id)
    {
        // Buscar incidencia por id
        $issue = TktIssue::findOrFail($id);

        // Validar los datos que vienen del formulario
        $validatedData = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('tkt_issues')->ignore($issue->id),
            ],
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        // Actualizar incidencia
        $issue->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'status' => $request->status ?? false, 
        ]);
        
        // Redirigir al listado de incidencias
        return redirect()->route('tickets.issues.index')
                         ->with('success', 'Incidencia actualizada correctamente.');
    }
}