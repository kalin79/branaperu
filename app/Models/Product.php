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
        'sku'
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function sections()
    {
        return $this->hasMany(ProductSection::class);
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
            ->withPivot('sort_order', 'is_active')           // ← cambiado
            ->withTimestamps()
            ->orderBy('product_feature.sort_order');         // ← cambiado
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
            ->withPivot('sort_order', 'is_active')           // ← cambiado
            ->withTimestamps()
            ->orderBy('product_related.sort_order');         // ← cambiado
    }
}