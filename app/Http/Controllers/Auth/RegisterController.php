<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function register()
    {
        return back()->with('error', 'Registro temporalmente deshabilitado');
    }
}