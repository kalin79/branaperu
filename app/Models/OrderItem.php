<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',           // Referencia útil para trazabilidad
        'sku',
        'product_name',         // Snapshot del nombre en el momento de la compra
        'product_slug',         // Para poder enlazar al producto original
        'product_image',        // Snapshot de la imagen del producto
        'ml',
        'quantity',
        'unit_price',           // Precio unitario pagado (final)
        'original_price',       // Precio original antes de cualquier descuento
        'subtotal',
        'notes',                // Notas específicas de esta línea
    ];

    protected $casts = [
        'quantity' => 'integer',
        'ml' => 'integer',
        'unit_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ====================== RELACIONES ======================
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ====================== HELPERS ======================
    public function getLineTotalAttribute(): float
    {
        return (float) ($this->subtotal ?? 0);
    }

    public function getUnitPriceFormattedAttribute(): string
    {
        return 'S/ ' . number_format($this->unit_price ?? 0, 2);
    }

    public function getOriginalPriceFormattedAttribute(): string
    {
        return 'S/ ' . number_format($this->original_price ?? $this->unit_price ?? 0, 2);
    }

    /**
     * Calcula el ahorro en esta línea (si hubo descuento)
     */
    public function getSavingsAttribute(): float
    {
        if (!$this->original_price || $this->original_price <= $this->unit_price) {
            return 0;
        }
        return round(($this->original_price - $this->unit_price) * $this->quantity, 2);
    }
}