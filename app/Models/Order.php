<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PAID => 'Pagado',
            self::STATUS_REJECTED => 'Pago Rechazado',
            self::STATUS_CANCELLED => 'Anulado',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'gray',
        ];
    }

    // ====================== SCOPES ======================
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
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

    // ====================== HELPERS ======================
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status ?? '');
    }

    public function getStatusColorAttribute(): string
    {
        return self::getStatusColors()[$this->status] ?? 'gray';
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

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
}