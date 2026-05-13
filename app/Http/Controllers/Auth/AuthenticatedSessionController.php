<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        if ($request->has('redirect')) {
            $redirect = $request->query('redirect');
            if (is_string($redirect) && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) {
                session(['url.intended' => $redirect]);
            }
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|SymfonyResponse
    {
        $request->authenticate();

        $user = $request->user();

        // Bloquear acceso si el usuario está bloqueado
        if ($user instanceof User && $user->isBlocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta está bloqueada. Contacta a soporte para más información.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $intended = session()->pull('url.intended');

        // Admin / Editor en prod → panel Filament
        if (app()->isProduction() && $user->hasAnyRole(['Administrador', 'Editor'])) {
            return Inertia::location(config('app.admin_panel_url'));
        }

        // Si vino de un flujo específico (ej. checkout) → respétalo
        if ($intended && $intended !== '/') {
            return redirect($intended);
        }

        // Por defecto → dashboard
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}