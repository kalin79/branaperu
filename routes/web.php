<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use Inertia\Inertia;

// ====================== PÁGINAS PÚBLICAS ======================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tienda', function () {
    return Inertia::render('Shop/Index');
})->name('shop');

Route::get('/producto/{slug}', function ($slug) {
    return Inertia::render('Product/Show', ['slug' => $slug]);
})->name('product.show');

Route::get('/locales', function () {
    return Inertia::render('Locals/Index');
})->name('locals');

// ====================== AUTENTICACIÓN ======================
Route::middleware('guest')->group(function () {

    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');

    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');

});

// Rutas de autenticación (POST)
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// ====================== RUTAS PROTEGIDAS (Usuario logueado) ======================
Route::middleware('auth')->group(function () {

    Route::get('/mi-cuenta', function () {
        return Inertia::render('Profile/Index');
    })->name('profile');

    Route::get('/mis-pedidos', function () {
        return Inertia::render('Orders/Index');
    })->name('orders');

    Route::get('/mis-pedidos/{order}', function ($order) {
        return Inertia::render('Orders/Show', ['order' => $order]);
    })->name('orders.show');
});

// ====================== PANEL ADMINISTRATIVO (Filament) ======================
Route::prefix('admin')->name('admin.')->group(function () {
    // Filament ya maneja sus propias rutas internamente
    // Solo dejamos esta ruta como referencia
});

// ====================== RUTAS ADICIONALES (puedes agregar más) ======================
Route::get('/contacto', function () {
    return Inertia::render('Contact');
})->name('contact');

// Fallback (404)
Route::fallback(function () {
    return Inertia::render('Errors/404');
});