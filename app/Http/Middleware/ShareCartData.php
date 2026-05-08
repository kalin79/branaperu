<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class ShareCartData
{
    public function handle(Request $request, Closure $next): Response
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        }

        Inertia::share([
            'cart' => $cart,
            'total' => $total,
        ]);

        return $next($request);
    }
}