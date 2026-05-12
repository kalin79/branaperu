<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\MayoristasController;
use App\Http\Controllers\Frontend\TerminosController;
use App\Http\Controllers\Frontend\EntregaController;
use App\Http\Controllers\Frontend\PrivacidadController;
use App\Http\Controllers\Frontend\CambioController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use App\Models\Payment;
// use App\Http\Controllers\PaymentExportController;

use Inertia\Inertia;

// ====================== FRONTEND - PÁGINAS PÚBLICAS ======================

Route::get('/', [HomeController::class, 'index'])->name('home');

// Productos
Route::get('/productos', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/productos/categoria/{slug}', [ProductController::class, 'showProductsByCategory'])
    ->name('products.by.category');

Route::get('/producto/{slug}', [ProductController::class, 'show'])
    ->name('product.show');

// Páginas estáticas
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

Route::get('/contacto', function () {
    return Inertia::render('Contact');
})->name('contact');

// ====================== CART ======================
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// ====================== E-coomerce ========================
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])
    ->name('checkout.process');
Route::get('/checkout/payment/{order_number?}', [CheckoutController::class, 'payment'])
    ->name('checkout.payment');

Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])
    ->name('checkout.success');
Route::get('/checkout/failure/{order_number}', [CheckoutController::class, 'failure'])
    ->name('checkout.failure');
Route::get('/checkout/pending/{order_number}', [CheckoutController::class, 'pending'])
    ->name('checkout.pending');


Route::post('/checkout/{order_number}/apply-coupon', [CheckoutController::class, 'applyCoupon'])
    ->name('checkout.applyCoupon');

Route::delete('/checkout/{order_number}/coupon', [CheckoutController::class, 'removeCoupon'])
    ->name('checkout.removeCoupon');

Route::post('/checkout/{order_number}/update-info', [CheckoutController::class, 'updateOrderInfo'])
    ->name('checkout.updateOrderInfo');

Route::post('/checkout/{order_number}/create-preference', [CheckoutController::class, 'createPreference'])
    ->name('checkout.createPreference');


// ====================== RUTAS PROTEGIDAS ======================

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


// ====================== EXPORTAR PAGOS ======================
Route::get('/export-payments', [App\Http\Controllers\PaymentExportController::class, 'export'])
    ->name('export.payments')
    ->middleware('auth');


// ====================== FALLBACK (404) ======================

Route::fallback(function () {
    return Inertia::render('Errors/404');
});

// ================== ARMANADO DEL AUTH FRONTNEND ======================


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index');
})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Impresión de comprobantes (solo administradores)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])
    ->prefix('admin/orders')
    ->name('admin.orders.')
    ->group(function () {
        Route::get('/{order}/print', function (Order $order) {
            // Permitir solo a Administrador (ajusta el rol si es distinto)
            abort_unless(
                auth()->user()?->hasRole('Administrador'),
                403
            );

            $order->load([
                'user',
                'items',
                'payments',
                'district',
                'pickupLocal',
                'coupon',
            ]);

            return view('admin.orders.print', compact('order'));
        })->name('print');
    });

Route::middleware(['web', 'auth'])
    ->prefix('admin/payments')
    ->name('admin.payments.')
    ->group(function () {
        Route::get('/{payment}/print', function (Payment $payment) {
            abort_unless(
                auth()->user()?->hasRole('Administrador'),
                403
            );

            $payment->load([
                'order' => fn($q) => $q->with(['user', 'items', 'district', 'pickupLocal', 'coupon', 'payments']),
            ]);

            return view('admin.payments.print', compact('payment'));
        })->name('print');
    });



require __DIR__ . '/auth.php';


