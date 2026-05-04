<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\MayoristasController;
use App\Http\Controllers\Frontend\TerminosController;
use App\Http\Controllers\Frontend\EntregaController;
use App\Http\Controllers\Frontend\PrivacidadController;
use App\Http\Controllers\Frontend\CambioController;
use App\Http\Controllers\Frontend\ProductController;
use Inertia\Inertia;

// ====================== PÁGINAS PÚBLICAS ======================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/acerca-de-brana', [AboutController::class, 'index'])
    ->name('acerca-de-brana');
Route::get('/ventas-mayorista', [MayoristasController::class, 'index'])
    ->name('ventas-mayorista');
Route::get('/terminos-y-condiciones', [TerminosController::class, 'index'])
    ->name('terminos-y-condiciones');
Route::get('/politica-de-entrega', [EntregaController::class, 'index'])
    ->name('politica-de-entrega');
Route::get('/politica-de-privacidad', [PrivacidadController::class, 'index'])
    ->name('politica-de-privacidad');
Route::get('/politica-de-cambio', [CambioController::class, 'index'])
    ->name('politica-de-cambio');
Route::get('/productos', [ProductController::class, 'index'])
    ->name('products.index');
// Esta ruta se usará vía Inertia para cargar productos paginados
Route::get('/productos/categoria/{slug}', [ProductController::class, 'showProductsByCategory'])
    ->name('products.by.category');

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