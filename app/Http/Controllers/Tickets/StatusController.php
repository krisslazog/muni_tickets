<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tkt_status;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatusController extends Controller
{
    
    // Mostrar listado de estados
    public function index(Request $request)
    {
        $status = Tkt_status::all();

        return Inertia::render('Tickets/Status/Index',
            [
                'status' => $status ,
            ]);
    }
    
     // Mostrar formulario para crear una nueva prioridad
    public function create()
    {
        return Inertia::render('Tickets/Status/Create');
    }

    //Crear nueva prioridad
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Crear la estado en la base de datos usando el modelo de estado
        Tkt_status::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de estado después de crearla
        return redirect()->route('tickets.status.index')
                         ->with('success', 'Estado creado correctamente.');
    }

    public function edit($id)
    {
        //Buscar prioridad por id
        $status = Tkt_status::findOrFail($id);
        //Retornar vista con la estado
        return Inertia::render('Tickets/Status/Edit', [
            'status' => $status,
        ]);
    }
    public function update(Request $request,$id)
    {

        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        //Buscar estado por id
        $priority = Tkt_status::findOrFail($id);

        //Actualizar estado
        $priority->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de estado después de actualizar
        return redirect()->route('tickets.status.index')
                         ->with('success', 'Estados actualizados correctamente.');
    }

}
