<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Product;
class HomeController extends Controller
{
    public function index()
    {
        // Solo categorías padres (top level)
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->withCount('products')                    // conteo directo
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $parentCategories->each(function ($category) {
            // Cargamos productos (padre + todas las subcategorías)
            $category->paginated_products = $this->getProductsForCategory($category);

            // Opcional: cargar subcategorías para mostrarlas en el frontend
            $category->load([
                'children' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('order')
                        ->orderBy('name');
                }
            ]);
        });

        return Inertia::render('Home', [
            'categories' => $parentCategories,
            'title_meta' => 'Acerca de Brana',
            'description_meta' => 'Texto descriptivo...',
        ]);
    }
    /**
     * Obtiene los productos de una categoría y todas sus subcategorías
     */
    private function getProductsForCategory(Category $category)
    {
        $categoryIds = $this->getCategoryTreeIds($category);

        return Product::whereIn('category_id', $categoryIds)
            ->with(['media'])
            ->active()                    // usa el scope que tienes en Product
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'page_' . $category->id)
            ->through(fn($product) => $product->append([
                'formatted_price',
                'formatted_old_price'
            ]));
    }

    /**
     * Retorna todos los IDs de la categoría y sus descendientes
     */
    private function getCategoryTreeIds(Category $category): array
    {
        $ids = [$category->id];

        // Cargamos hijos activos
        $children = Category::where('parent_id', $category->id)
            ->where('is_active', true)
            ->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getCategoryTreeIds($child));
        }

        return array_unique($ids);
    }
}