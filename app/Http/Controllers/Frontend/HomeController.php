<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\PersonalCareSection;
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

        $banners = Banner::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn($b) => [
                'titulo' => $b->title,
                'tipo' => $b->type,
                'imagepc' => $b->image_pc ? asset('storage/' . $b->image_pc) : null,
                'imagemobile' => $b->image_mobile ? asset('storage/' . $b->image_mobile) : null,
                'video_file' => $b->video_file ? asset('storage/' . $b->video_file) : null,
                'youtube_url' => $b->youtube_url,
            ]);

        $personalCare = PersonalCareSection::with([
            'features' => fn($q) => $q->where('is_active', true)->orderBy('order')
        ])->first();

        $cuidados = $personalCare ? [
            'titulo' => $personalCare->title,
            'subtitulo' => $personalCare->subtitle,
            'descripcion' => $personalCare->description,
            'icono' => $personalCare->icon ? asset('storage/' . $personalCare->icon) : null,
            'imagen_fondo' => $personalCare->background_image ? asset('storage/' . $personalCare->background_image) : null,
            'caracteristicas' => $personalCare->features->map(fn($f) => [
                'titulo' => $f->title,
                'descripcion' => $f->description,
                'icono' => $f->icon ? asset('storage/' . $f->icon) : null,
                'color' => $f->color,
            ])->values(),
        ] : null;

        return Inertia::render('Home', [
            'categories' => $parentCategories,
            'banners' => $banners,
            'cuidados' => $cuidados,
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