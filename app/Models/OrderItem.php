<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'sku',
        'product_name',
        'ml',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'ml' => 'integer',
        'unit_price' => 'decimal:2',
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
}