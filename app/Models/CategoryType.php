<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CategoryType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($categoryType) {
            if (empty($categoryType->slug)) {
                $categoryType->slug = Str::slug($categoryType->name);
            }
        });

        static::updating(function ($categoryType) {
            if ($categoryType->isDirty('name') && empty($categoryType->slug)) {
                $categoryType->slug = Str::slug($categoryType->name);
            }
        });
    }

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ====================== RELACIONES ======================

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}