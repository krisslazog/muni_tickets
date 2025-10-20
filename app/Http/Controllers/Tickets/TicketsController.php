<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tkt_ticket;
use App\Models\Tkt_category;
use App\Models\Tkt_priority;
use App\Models\Tkt_notifications;
use App\Models\Tkt_status;
use App\Models\Tkt_attachment;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TicketsController extends Controller
{
    /**
     * Muestra la tabla con la lista de todos los tickets.
     */
    public function index()
    {
        $tickets = Tkt_ticket::with(['category', 'priority', 'status', 'requester'])
                             ->latest() 
                             ->paginate(15);

        // Esta es la versión correcta que SÍ envía los datos para los combos/filtros
        return Inertia::render('Tickets/Tickets/Index', [
            'tickets' => $tickets,
            'categories' => Tkt_category::all(),
            'priorities' => Tkt_priority::all(),
            'statuses' => Tkt_status::all(),
            'areas' => Area::all(),
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo ticket.
     */
    public function create()
    {
        return Inertia::render('Tickets/Tickets/Create', [
            'categories' => Tkt_category::all(),
            'priorities' => Tkt_priority::all(),
            'areas' => Area::all(['id', 'name']),
        ]);
    }

    /**
     * Almacena un nuevo ticket en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'category_id' => 'required|exists:tkt_categories,id',
            'priority_id' => 'required|exists:tkt_priorities,id',
            'area_id'         => 'nullable|exists:areas,id', // Lo dejamos opcional (nullable)
            'attachment'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validamos el adjunto
        ]);

        // 2. Creación del ticket
        $ticket = Tkt_ticket::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'priority_id' => $validatedData['priority_id'],
            'area_id' => 1,
            'status_id' => 1, // Asigna un estado inicial por defecto (ej: ID 1 = "Abierto")
            'requester_id' => Auth::id(), // Asigna el ID del usuario autenticado
            'assignee_id' => 1, // Sin asignar inicialmente
        ]);

        // 3. Guardar adjunto
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('attachments', 'public');

            $ticket->attachments()->create([
                'file_name' => $originalName,
                'file_path' => $path,
            ]);
        }
        // 4. Redirección con mensaje de éxito
        return to_route('tickets.tickets.index')->with('success', '¡Ticket creado exitosamente!');
    }
}