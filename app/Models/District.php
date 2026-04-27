<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'ubigeo',
        'department',
        'province',
        'district',
        'delivery_cost',
        'is_active',
    ];

    protected $casts = [
        'delivery_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ====================== HELPERS ======================

    /**
     * ✅ Costo EFECTIVO (el que se usa realmente para calcular)
     * (0 = usar costo global)
     */
    public function getEffectiveDeliveryCostAttribute(): float
    {
        return $this->delivery_cost > 0
            ? (float) $this->delivery_cost
            : DeliveryConfiguration::defaultDeliveryCost();
    }

    /**
     * Nombre completo para mostrar
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->department} - {$this->province} - {$this->district}";
    }
}