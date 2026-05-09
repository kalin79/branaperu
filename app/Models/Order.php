<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Payment;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',

        // Montos
        'subtotal',
        'discount_amount',
        'final_total',

        // Snapshot del cupón
        'coupon_id',
        'coupon_code',
        'coupon_name',
        'coupon_discount_value',

        // Snapshot de la regla de descuento automático
        'discount_rule_name',
        'discount_rule_min_amount',
        'discount_rule_percent',

        // Estado y pago
        'status',
        'payment_id',
        'payment_response',

        // Cliente
        'guest_name',
        'guest_last_name',     // ← NUEVO
        'guest_email',
        'guest_phone',
        'dni',                 // ← NUEVO

        // Método de entrega
        'delivery_method',     // ← NUEVO: 'delivery' | 'pickup'

        // Si delivery
        'delivery_district_id',
        'delivery_cost',
        'shipping_address',
        'delivery_reference',
        'delivery_full_name',

        // Si pickup (snapshot del local)
        'pickup_local_id',       // ← NUEVO
        'pickup_local_name',     // ← NUEVO
        'pickup_local_address',  // ← NUEVO

        // Documento
        'document_type',         // ← NUEVO: 'boleta' | 'factura'
        'billing_ruc',           // ← NUEVO
        'billing_business_name', // ← NUEVO
        'billing_address',       // ← NUEVO

        // Consentimientos
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

    // ====================== ESTADOS DE LA ORDEN ======================
    const STATUS_PENDING = 'pending';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_PREPARING = 'preparing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_RETURNED = 'returned';

    // ====================== MÉTODOS DE ENTREGA ======================
    const DELIVERY_METHOD_DELIVERY = 'delivery';
    const DELIVERY_METHOD_PICKUP = 'pickup';

    public static function getDeliveryMethodOptions(): array
    {
        return [
            self::DELIVERY_METHOD_DELIVERY => 'Envío a domicilio',
            self::DELIVERY_METHOD_PICKUP => 'Retiro en tienda',
        ];
    }

    // ====================== TIPOS DE DOCUMENTO ======================
    const DOCUMENT_TYPE_BOLETA = 'boleta';
    const DOCUMENT_TYPE_FACTURA = 'factura';

    public static function getDocumentTypeOptions(): array
    {
        return [
            self::DOCUMENT_TYPE_BOLETA => 'Boleta',
            self::DOCUMENT_TYPE_FACTURA => 'Factura',
        ];
    }

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
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

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

    public function pickupLocal(): BelongsTo  // ← NUEVA
    {
        return $this->belongsTo(Local::class, 'pickup_local_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function currentPayment()
    {
        return $this->hasOne(Payment::class)->latest('id');
    }

    public function latestPaymentForFilter()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
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

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    // === Helpers método de entrega ===
    public function isDelivery(): bool
    {
        return $this->delivery_method === self::DELIVERY_METHOD_DELIVERY;
    }

    public function isPickup(): bool
    {
        return $this->delivery_method === self::DELIVERY_METHOD_PICKUP;
    }

    // === Helpers documento ===
    public function isBoleta(): bool
    {
        return $this->document_type === self::DOCUMENT_TYPE_BOLETA;
    }

    public function isFactura(): bool
    {
        return $this->document_type === self::DOCUMENT_TYPE_FACTURA;
    }

    // === Helpers descuento ===
    public function hasCouponApplied(): bool
    {
        return !empty($this->coupon_code);
    }

    public function hasAutoDiscountApplied(): bool
    {
        return !empty($this->discount_rule_name);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->final_total;
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user?->name
            ?? trim(($this->guest_name ?? '') . ' ' . ($this->guest_last_name ?? ''))
            ?: 'Cliente invitado';
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function hasPayments(): bool
    {
        return $this->payments()->exists();
    }

    public function getLatestPaymentAttribute()
    {
        return $this->latestPayment()->first();
    }
}