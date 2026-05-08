<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Temporal - puedes completarlo después
        return back()->with('error', 'Login no implementado aún');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect()->route('login');
    }
}