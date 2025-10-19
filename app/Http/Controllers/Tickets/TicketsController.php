<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tkt_ticket;
use App\Models\Tkt_category;
use App\Models\Tkt_priority;
use App\Models\Tkt_notifications; // Este no se usa aquí, pero es bueno tenerlo
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
        ]);
    }

    /**
     * Almacena un nuevo ticket en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tkt_category_id' => 'required|exists:tkt_categories,id',
            'tkt_priority_id' => 'required|exists:tkt_priorities,id',
        ]);

        // 2. Creación del ticket
        Tkt_ticket::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'tkt_category_id' => $validatedData['tkt_category_id'],
            'tkt_priority_id' => $validatedData['tkt_priority_id'],
            'tkt_status_id' => 1, // Asigna un estado inicial por defecto (ej: ID 1 = "Abierto")
            'requester_id' => Auth::id(), // Asigna el ID del usuario autenticado
        ]);

        // 3. Redirección con mensaje de éxito
        return to_route('tickets.index')->with('success', '¡Ticket creado exitosamente!');
    }
}