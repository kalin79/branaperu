<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TerminosController extends Controller
{
    public function index()
    {
        return Inertia::render('Legal/TerminosCondiciones', [
            'title_meta' => 'Términos y Condiciones',
            'description_meta' => 'Lee nuestros términos y condiciones de uso de Brana.',
            // Puedes agregar más datos si los necesitas (fecha de actualización, etc.)
        ]);
    }
}