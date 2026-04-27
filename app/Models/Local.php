<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'address',
        'google_maps_link',
        'label',
        'short_description',
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
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    // ====================== HELPERS ======================
    public function getFullAddressAttribute(): string
    {
        return $this->address;
    }
}