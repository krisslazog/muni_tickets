<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tkt_category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Tkt_category::all();

        return Inertia::render('Tickets/Category/Index',
            [
                'categories' => $categories,
            ]);
    }
}
