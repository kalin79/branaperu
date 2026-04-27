<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_amount',
        'discount_percent',
        'is_active'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ====================== HELPERS ======================

    /**
     * Verifica si esta regla de descuento aplica a un subtotal dado
     * (Útil para el carrito de compras)
     */
    public function appliesTo(float $subtotal): bool
    {
        return $this->is_active && $subtotal >= $this->min_amount;
    }

    /**
     * Calcula el monto de descuento para un subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->appliesTo($subtotal)) {
            return 0;
        }

        return round($subtotal * ($this->discount_percent / 100), 2);
    }
}