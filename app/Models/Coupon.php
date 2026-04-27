<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'discount_value',
        'discount_type',
        'min_purchase_amount',
        'max_uses',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ====================== SCOPES ======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    // ====================== HELPERS ======================

    /**
     * Verifica si el cupón es válido en este momento
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Calcula el monto de descuento según el tipo (percent o fixed)
     * y el subtotal del carrito
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid() || $subtotal < $this->min_purchase_amount) {
            return 0;
        }

        return match ($this->discount_type) {
            'percent' => round($subtotal * ($this->discount_value / 100), 2),
            'fixed' => min($this->discount_value, $subtotal), // no puede ser mayor que el subtotal
            default => 0,
        };
    }
}