<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/Index', [
            'parentCategories' => $this->getParentCategoriesWithCounts(),
            'selectedCategory' => null,
            'products' => null,
            'title_meta' => 'Todos los Productos - Brana Perú',
            'description_meta' => 'Todos nuestros productos'
        ]);
    }

    public function showProductsByCategory(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Obtenemos los IDs de categorías a consultar
        $categoryIds = is_null($category->parent_id)
            ? $this->getCategoryTreeIds($category)           // Padre → incluye subcategorías
            : [$category->id];                               // Hijo → solo él mismo

        $products = Product::whereIn('category_id', $categoryIds)
            ->with(['media'])
            ->active()
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->through(fn($product) => $product->append([
                'formatted_price',
                'formatted_old_price',
                'cover_image_url'
            ]));

        // Si es categoría hija, agregamos el color del padre
        if (!is_null($category->parent_id) && $category->parent) {
            $category->parent_color = $category->parent->color;
        } else {
            $category->parent_color = $category->color; // si es padre, usa su propio color
        }

        return Inertia::render('Products/Index', [
            'parentCategories' => $this->getParentCategoriesWithCounts(),
            'selectedCategory' => $category,
            'products' => $products,
        ]);
    }

    /**
     * Retorna categorías padre con conteo total de productos
     */
    private function getParentCategoriesWithCounts()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'children' => fn($q) => $q->where('is_active', true)
                    ->orderBy('order')
                    ->orderBy('name')
                    ->withCount('products')
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $parentCategories->each(function ($category) {
            $categoryIds = $this->getCategoryTreeIds($category);

            $category->products_count = Product::whereIn('category_id', $categoryIds)
                ->active()
                ->count();

            $category->carousel_products = Product::whereIn('category_id', $categoryIds)
                ->with(['media'])
                ->active()
                ->orderBy('order')
                ->orderByDesc('created_at')
                ->take(10)
                ->get()
                ->each->append([
                        'formatted_price',
                        'formatted_old_price',
                        'cover_image_url'
                    ]);
        });

        return $parentCategories;
    }

    /**
     * Árbol de IDs (categoría + todas las subcategorías) - IMPORTANTE
     */
    private function getCategoryTreeIds(Category $category): array
    {
        $ids = [$category->id];

        $children = Category::where('parent_id', $category->id)
            ->where('is_active', true)
            ->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getCategoryTreeIds($child));
        }

        return array_unique($ids);
    }
    /**
     * Detalle del producto
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category' => fn($q) => $q->with('parent'),
                'media' => fn($q) => $q->where('is_active', true)->orderBy('order'),
                'sections' => fn($q) => $q->where('is_active', true)->orderBy('orden'),
                'features' => fn($q) => $q->wherePivot('is_active', true)
                    ->orderBy('product_feature.sort_order'),
                'relatedProductsFrontend' => fn($q) => $q
                    ->with('media')
                    ->take(8)
            ])
            ->firstOrFail();

        // Append accesores
        $product->append([
            'formatted_price',
            'formatted_old_price',
            'cover_image_url'
        ]);

        // ✅ Append para TODOS los productos relacionados
        $product->relatedProductsFrontend->each(function ($relatedProduct) {
            $relatedProduct->append([
                'formatted_price',
                'formatted_old_price',
                'cover_image_url'
            ]);
        });

        return Inertia::render('Products/Show', [
            'product' => $product,
            'title_meta' => $product->meta_title ?? $product->name,
            'description_meta' => $product->meta_description ?? Str::limit(strip_tags($product->short_description), 160),
        ]);
    }
}