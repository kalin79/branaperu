<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'title',
        'alt_text',
        'media_type',
        'file_url',
        'video_id',
        'thumbnail_url',
        'is_main',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ====================== RELACIONES ======================

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}