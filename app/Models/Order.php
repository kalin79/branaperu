<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasOne;    // ← Agregar
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Payment;     // ← AGREGAR ESTA LÍNEA

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',                    // null = compra como invitado
        'order_number',

        // Montos
        'subtotal',
        'discount_amount',
        'final_total',

        // === SNAPSHOT DEL CUPÓN ===
        'coupon_id',
        'coupon_code',
        'coupon_name',
        'coupon_discount_value',

        // === SNAPSHOT DE LA REGLA DE DESCUENTO AUTOMÁTICO ===
        'discount_rule_name',
        'discount_rule_min_amount',
        'discount_rule_percent',

        // Estado y pago
        'status',
        'payment_id',
        'payment_response',

        // Información del cliente (invitado o registrado)
        'guest_name',
        'guest_email',
        'guest_phone',

        // Delivery
        'delivery_district_id',
        'delivery_cost',
        'shipping_address',
        'delivery_reference',
        'delivery_full_name',

        // Consentimientos del usuario (importante para trazabilidad legal)
        'accepted_terms',
        'accepted_privacy',
        'accepted_marketing',

        'notes',
    ];


    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'coupon_discount_value' => 'decimal:2',
        'discount_rule_min_amount' => 'decimal:2',
        'discount_rule_percent' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'payment_response' => 'array',
        'accepted_terms' => 'boolean',
        'accepted_privacy' => 'boolean',
        'accepted_marketing' => 'boolean',
    ];

    // ====================== ESTADOS ======================
    const STATUS_PENDING = 'pending';      // Usuario inició el checkout (en formulario de pago)
    const STATUS_ABANDONED = 'abandoned';    // Usuario abandonó el proceso de pago
    const STATUS_PREPARING = 'preparing';    // Ya pagó → preparando pedido
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_RETURNED = 'returned';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'En Checkout',
            self::STATUS_ABANDONED => 'Carrito Abandonado',
            self::STATUS_PREPARING => 'Preparando',
            self::STATUS_SHIPPED => 'Enviado',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_REFUNDED => 'Reembolsado',
            self::STATUS_RETURNED => 'Devuelto',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_ABANDONED => 'gray',
            self::STATUS_PREPARING => 'info',
            self::STATUS_SHIPPED => 'primary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_REFUNDED => 'danger',
            self::STATUS_RETURNED => 'danger',
        ];
    }

    // ====================== SCOPES ======================
    // public function scopePaid($query)
    // {
    //     return $query->where('status', self::STATUS_PAID);
    // }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // public function scopeSuccessful($query)
    // {
    //     return $query->where('status', self::STATUS_PAID);
    // }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // Nuevo: Órdenes cuyo último pago fue rechazado
    public function scopeWithRejectedPayment($query)
    {
        return $query->whereHas('latestPayment', function ($q) {
            $q->where('status', Payment::STATUS_REJECTED);
        });
    }

    // ====================== RELACIONES ======================
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'delivery_district_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    // Versión alternativa para tablas (más estable)
    public function currentPayment()
    {
        return $this->hasOne(Payment::class)
            ->latest('id');   // Ordena por ID descendente
    }

    // ====================== HELPERS ======================
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status ?? '');
    }

    public function getStatusColorAttribute(): string
    {
        return self::getStatusColors()[$this->status] ?? 'gray';
    }

    // public function isPaid(): bool
    // {
    //     return $this->status === self::STATUS_PAID;
    // }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->final_total;
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Cliente invitado';
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    /**
     * CAMPOS DEPRECATED (se mantendrán por compatibilidad temporal)
     * 
     * Ya no se deben usar para nueva lógica:
     * - payment_id
     * - payment_response
     * - status  (ahora solo representa el estado del PEDIDO)
     */

    // Helper para saber si ya tiene pagos en la nueva estructura
    public function hasPayments(): bool
    {
        return $this->payments()->exists();
    }

    /**
     * Retorna el último pago (recomendado usar este en vez de payment_id)
     */
    public function getLatestPaymentAttribute()
    {
        return $this->latestPayment();   // ← Mejorado
    }
    // Relación para filtros de Filament (necesaria porque latestOfMany no funciona bien en filtros)
    public function latestPaymentForFilter()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}