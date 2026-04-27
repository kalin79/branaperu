<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'section_type',
        'name',
        'description',
        'content',
        'media_type',
        'file_media',
        'video_id',
        'settings',
        'orden',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'orden' => 'integer',
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