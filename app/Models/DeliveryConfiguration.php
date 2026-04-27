<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_delivery_cost',
        'free_shipping_threshold',
        'is_active',
    ];

    protected $casts = [
        'default_delivery_cost' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Solo queremos una fila en esta tabla
    public static function getInstance(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'default_delivery_cost' => 10.00,
                'is_active' => true,
            ]
        );
    }

    public static function defaultDeliveryCost(): float
    {
        return (float) self::getInstance()->default_delivery_cost;
    }

    public static function freeShippingThreshold(): ?float
    {
        return self::getInstance()->free_shipping_threshold;
    }
}