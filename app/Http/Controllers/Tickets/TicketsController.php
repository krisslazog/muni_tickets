<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tkt_ticket;
use App\Models\Tkt_category;
use App\Models\Tkt_priority; 
use App\Models\Tkt_notifications;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // Para saber quién crea el ticket

class TicketsController extends Controller
{

    // Muestra el formulario para crear un nuevo ticket.
    public function create()
    {
    // Carga las categorías y prioridades
    $categories = Tkt_category::all();
   // $priorities = Tkt_priority::all();
   $priorities = \App\Models\Tkt_priority::withoutGlobalScopes()->get();
    dd($priorities);
        return Inertia::render('Tickets/Tickets/Create', [
            'categories' => Tkt_category::all(),
            'priorities' => Tkt_priority::all(),
        ]);
    }

    // Almacena un nuevo ticket en la base de datos.
    public function store(Request $request)
    {
        // 1. Validación de los datos que vienen del formulario
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tkt_category_id' => 'required|exists:tkt_categories,id',
            'tkt_priority_id' => 'required|exists:tkt_priorities,id',
            // 'area_id' => 'required|exists:areas,id', 
        ]);

        // 2. Creación del ticket en la base de datos
        Tkt_ticket::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'tkt_category_id' => $validatedData['tkt_category_id'],
            'tkt_priority_id' => $validatedData['tkt_priority_id'],
            'tkt_status_id' => 1, // Asigna un estado inicial por defecto (ej: ID 1 = "Abierto")
            'requester_id' => Auth::id(), // Asigna el ID del usuario autenticado como solicitante
            // 'area_id' => $validatedData['area_id'],
        ]);

        // 3. Redirección a la página principal de tickets con un mensaje de éxito.
        return to_route('tickets.index')->with('success', '¡Ticket creado exitosamente!');
    }

    // Aquí irían los otros métodos que ya tienes (index, edit, update, etc.)
    public function index()
    {
        $tickets = Tkt_ticket::with(['category', 'priority', 'status', 'requester'])->latest()->paginate(15);
        return Inertia::render('Tickets/Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }
}