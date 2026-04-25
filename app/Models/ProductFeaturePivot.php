<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductFeaturePivot extends Pivot
{
    protected $table = 'product_feature';

    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'feature_id',
        'orden',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}