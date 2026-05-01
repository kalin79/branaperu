<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MayoristasController extends Controller
{
    public function index()
    {
        return Inertia::render('Mayoristas', [
            // Aquí pasas los datos que necesites
            'title_meta' => 'Ventas Mayorista',
            'description_meta' => 'Texto descriptivo...',
            // Puedes pasar cualquier dato: equipo, historia, etc.
        ]);
    }
}
