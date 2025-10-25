<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TktPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class PriorityController extends Controller
{
    
    // Mostrar listado de prioridades
    public function index(Request $request)
    {
    $priorities = TktPriority::with('createdBy', 'updatedBy')
            ->paginate(10);

        return Inertia::render('Tickets/Priority/Index', [
            'priorities' => $priorities,
        ]);
    }
    
     // Mostrar formulario para crear una nueva prioridad
    public function create()
    {
        return Inertia::render('Tickets/Priority/Create');
    }

    //Crear nueva prioridad
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $validatedData = $request->validate([
            // 'tkt_priorities' es el nombre de tu tabla
            'name' => 'required|string|max:255|unique:tkt_priorities',
            'description' => 'nullable|string',
            'status' => 'required|boolean', // <-- 4. Mejorado
        ]);
        // Crear la categoría en la base de datos usando el modelo prioridad
        TktPriority::create($validatedData);
        
        return redirect()->route('tickets.priority.index')
                         ->with('success', 'Prioridad creada correctamente.');
    }

    public function edit($id)
    {
        //Buscar prioridad por id
        $priority = TktPriority::findOrFail($id);
        //Retornar vista con la prioridad
        return Inertia::render('Tickets/Priority/Edit', [
            'priority' => $priority,
        ]);
    }
    public function update(Request $request, $id)
    {
        // Buscar prioridad por id
        $priority = TktPriority::findOrFail($id);

        $validatedData = $request->validate([
            'name' => [
                'required', 'string', 'max:255',

                Rule::unique('tkt_priorities')->ignore($priority->id),
            ],
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $priority->update($validatedData);
        
        return redirect()->route('tickets.priority.index')
                         ->with('success', 'Prioridad actualizada correctamente.');
    }

}
