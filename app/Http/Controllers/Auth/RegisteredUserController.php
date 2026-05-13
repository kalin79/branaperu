<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        if ($request->has('redirect')) {
            $redirect = $request->query('redirect');
            if (is_string($redirect) && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) {
                session(['url.intended' => $redirect]);
            }
        }

        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'min:9', 'max:20', 'regex:/^[0-9+\s\-]+$/'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => User::STATUS_ACTIVE,
        ]);

        if (\Spatie\Permission\Models\Role::where('name', 'Cliente')->exists()) {
            $user->assignRole('Cliente');
        }

        event(new Registered($user));
        Auth::login($user);

        // Si vino con un redirect específico (ej. checkout), respétalo
        $intended = session()->pull('url.intended');

        // ↓ TEMPORAL — pega esto
        // dd([
        //     'intended' => $intended,
        //     'dashboard_url' => route('dashboard'),
        //     'env' => app()->environment(),
        // ]);

        if ($intended && $intended !== '/') {
            return redirect($intended);
        }

        return redirect()->route('dashboard');

    }
}