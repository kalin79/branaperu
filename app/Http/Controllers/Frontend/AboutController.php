<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        return Inertia::render('AcercaDeBrana', [
            // Aquí pasas los datos que necesites
            'title_meta' => 'Acerca de Brana',
            'description_meta' => 'Texto descriptivo...',
            // Puedes pasar cualquier dato: equipo, historia, etc.
        ]);
    }
}