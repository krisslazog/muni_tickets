<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tkt_ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketsController extends Controller
{
    // Mostrar listado de estados
    public function index(Request $request)
    {
        $ticket = Tkt_ticket::all();

         return Inertia::render('Tickets/Tickets/Index',
            [
                'tickets' => $ticket ,
           ]);
    }
}
