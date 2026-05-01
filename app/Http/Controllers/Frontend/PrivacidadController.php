<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrivacidadController extends Controller
{
    public function index()
    {
        return Inertia::render('Legal/Privacidad', [
            'title_meta' => 'Politicias de Privacidad',
            'description_meta' => 'Lee nuestros términos y condiciones de uso de Brana.',
            // Puedes agregar más datos si los necesitas (fecha de actualización, etc.)
        ]);
    }
}