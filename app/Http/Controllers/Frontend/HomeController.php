<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('order')           // Mejor usar el campo order si lo tienes
            ->orderBy('name')
            ->get();

        // Cargamos productos paginados (20 por página) por cada categoría
        $categories->each(function ($category) {
            $category->paginated_products = $category->products()
                ->with(['media'])                    // Imágenes del producto
                ->latest('created_at')
                ->paginate(20, ['*'], 'page_' . $category->id);
        });

        return Inertia::render('Home', [
            'categories' => $categories,
        ]);
    }
}