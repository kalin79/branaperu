<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'provider',
        'external_id',
        'status',
        'amount',
        'currency',
        'payment_method',
        'payment_response',
        'paid_at',
        'failed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_response' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // ====================== ESTADOS ======================
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CHARGEBACK = 'chargeback';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_APPROVED => 'Aprobado',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_IN_PROCESS => 'En Proceso',
            self::STATUS_REFUNDED => 'Reembolsado',
            self::STATUS_CHARGEBACK => 'Chargeback',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [self::STATUS_REJECTED, self::STATUS_CHARGEBACK]);
    }
    /**
     * Retorna el nombre legible del estado del pago
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status ?? 'Desconocido');
    }
}