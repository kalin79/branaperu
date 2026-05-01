<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CambioController extends Controller
{
    public function index()
    {
        return Inertia::render('Legal/Cambio', [
            'title_meta' => 'Politicias de Cambio',
            'description_meta' => 'Lee nuestros términos y condiciones de uso de Brana.',
            // Puedes agregar más datos si los necesitas (fecha de actualización, etc.)
        ]);
    }
}