<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'cover_image',
        'subtitle',
        'price',
        'old_price',
        'short_description',
        'description',
        'order',
        'is_active',
        'meta_title',
        'meta_description',
        'featured',
        'stock',
        'sku',
        'ml',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'stock' => 'integer',
        'order' => 'integer',
        'ml' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // ====================== ACCESORES ======================

    /**
     * URL completa de la imagen de portada
     */
    public function getCoverImageUrlAttribute(): string
    {
        if (!empty($this->cover_image)) {
            return asset('storage/' . $this->cover_image);
        }

        return asset('images/no-image.png');
    }
    /**
     * Precio formateado: S/ 39.00
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'S/ ' . number_format((float) $this->price, 2, '.', ',');
    }

    /**
     * Precio anterior formateado (opcional)
     */
    public function getFormattedOldPriceAttribute(): ?string
    {
        if (!$this->old_price) {
            return null;
        }
        return 'S/ ' . number_format((float) $this->old_price, 2, '.', ',');
    }

    // ====================== RELACIONES ======================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class)
            ->orderBy('order');
    }

    public function sections()
    {
        return $this->hasMany(ProductSection::class)
            ->orderBy('orden');
    }

    public function features()
    {
        return $this->belongsToMany(
            ProductFeature::class,
            'product_feature',
            'product_id',
            'feature_id'
        )
            ->using(ProductFeaturePivot::class)
            ->withPivot('sort_order', 'is_active')
            ->withTimestamps()
            ->orderBy('product_feature.sort_order');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_related',
            'product_id',
            'related_product_id'
        )
            ->using(ProductRelated::class)
            ->withPivot('sort_order', 'is_active')
            ->withTimestamps()
            ->orderBy('product_related.sort_order');
    }

    // ====================== HELPERS ======================

    /**
     * Precio actual (útil para carrito y vistas)
     * Se corrigió el tipo de retorno
     */
    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->price ?? 0);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}