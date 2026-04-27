<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFeature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ====================== RELACIONES ======================

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_feature')
            ->using(ProductFeaturePivot::class)
            ->withPivot('sort_order', 'is_active')
            ->withTimestamps()
            ->orderBy('product_feature.sort_order');
    }
}