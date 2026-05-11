<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Obtenemos la información del producto
        $product = Product::findOrFail($productId);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'subtitle' => $product->subtitle ?? '',
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'cover_image' => $product->cover_image,
                'ml' => $product->ml ?? '',
                // ✅ NUEVO: Nombre de la categoría
                'category_name' => $product->category?->name ?? 'Sin categoría',
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Producto añadido al carrito correctamente');
    }

    // Eliminar producto del carrito
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Producto eliminado del carrito');
    }

    // Actualizar cantidad
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Cantidad actualizada');
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        $total = 0;

        if (!empty($cart)) {
            return redirect()->route('checkout.index');
        }

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
            'total' => $total,
            'title_meta' => 'Carrito vacio',
            'description_meta' => 'No hay productos seleccionados...',
        ]);
    }
}