<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TktTicket;
use App\Models\TktCategory;
use App\Models\TktPriority;
use App\Models\TktStatus;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // <-- 1. IMPORTACIÓN AÑADIDA
use Illuminate\Support\Facades\Redirect; // <-- 2. IMPORTACIÓN AÑADIDA
use Illuminate\Support\Facades\Route; // <-- 3. IMPORTACIÓN AÑADIDA

class TicketsController extends Controller
{
    /**
     * Muestra la tabla con la lista de todos los tickets.  
     */
    public function index()
    {
        $tickets = TktTicket::with(['category', 'priority', 'status', 'requester'])
                             ->latest() 
                             ->paginate(15);

        // Esta es la versión correcta que SÍ envía los datos para los combos/filtros
        return Inertia::render('Tickets/Tickets/Index', [
            'tickets' => $tickets,
            'categories' => TktCategory::all(),
            'priorities' => TktPriority::all(),
            'statuses' => TktStatus::all(),
            'areas' => Area::all(),
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo ticket.
     */
    public function create()
    {
        return Inertia::render('Tickets/Tickets/Create', [
            'categories' => TktCategory::all(),
            'priorities' => TktPriority::all(),
            'areas' => Area::all(['id', 'name']),
        ]);
    }

    /**
     * Almacena un nuevo ticket en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos de entrada
        $validatedData = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'category_id'   => 'required|integer|exists:tkt_categories,id',
            'priority_id'   => 'required|integer|exists:tkt_priorities,id',
            'area_id'       => 'required|exists:areas,id',
            'attachments'   => 'nullable|array', // Valida que 'attachments' sea un array si se envía
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Valida cada archivo en el array
        ]);

        // 2. Obtener el estado inicial de forma dinámica
        $initialStatus = TktStatus::where('name', 'Abierto')->first();
        if (!$initialStatus) {
            return back()->withErrors(['status_id' => 'Error: No se encontró el estado inicial "Abierto".']);
        }

        // 3. Creación del ticket con los datos correctos
        $ticket = TktTicket::create([
            'title'         => $validatedData['title'],
            'description'   => $validatedData['description'],
            'category_id'   => $validatedData['category_id'],
            'priority_id'   => $validatedData['priority_id'],
            'area_id'       => $validatedData['area_id'],
            'status_id'     => $initialStatus->id,
            'requester_id' => Auth::id(),
            'assignee_id'   => 1,      // CORRECTO: Sin asignar inicialmente
        ]);

        // 4. Guardar adjuntos (si existen) de forma más limpia
        if ($request->hasFile('attachments')) {
            $ticket->addMultipleMediaFromRequest(['attachments'])
                   ->each(fn ($fileAdder) => $fileAdder->toMediaCollection('attachments'));
        }
        
        // 5. Redirección con mensaje de éxito
        return to_route('tickets.tickets.index')->with('success', '¡Ticket creado exitosamente!');
        

    }
}