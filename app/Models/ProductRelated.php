<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductRelated extends Pivot
{
    protected $table = 'product_related';

    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'related_product_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    // public function relatedProducts()
    // {
    //     return $this->belongsToMany(Product::class, 'product_related', 
    //         'product_id', 'related_product_id')
    //         ->using(ProductRelated::class)
    //         ->withPivot('order', 'is_active')
    //         ->withTimestamps()
    //         ->orderBy('product_related.order');
    // }
}