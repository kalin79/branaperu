<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Claim extends Model
{
    use HasFactory, LogsActivity;

    // ========= TIPOS =========
    const TYPE_RECLAMO = 'reclamo';
    const TYPE_QUEJA = 'queja';

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_RECLAMO => 'Reclamo',
            self::TYPE_QUEJA => 'Queja',
        ];
    }

    // ========= ESTADOS =========
    const STATUS_PENDIENTE = 'pendiente';
    const STATUS_EN_REVISION = 'en_revision';
    const STATUS_ATENDIDO = 'atendido';
    const STATUS_RECHAZADO = 'rechazado';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDIENTE => 'Pendiente',
            self::STATUS_EN_REVISION => 'En revisión',
            self::STATUS_ATENDIDO => 'Atendido',
            self::STATUS_RECHAZADO => 'Rechazado',
        ];
    }

    // ========= TIPOS DE DOCUMENTO =========
    const DOC_DNI = 'DNI';
    const DOC_CE = 'CE';
    const DOC_PASAPORTE = 'PASAPORTE';
    const DOC_RUC = 'RUC';

    public static function getDocumentTypeOptions(): array
    {
        return [
            self::DOC_DNI => 'DNI',
            self::DOC_CE => 'Carnet de Extranjería',
            self::DOC_PASAPORTE => 'Pasaporte',
            self::DOC_RUC => 'RUC',
        ];
    }

    // ========= LEGAL =========
    const LEGAL_DEADLINE_DAYS = 15; // días hábiles máximos para responder
    const WARNING_DEADLINE_DAYS = 13; // umbral de alerta (2 días antes)

    protected $fillable = [
        'claim_number',
        'claim_type',
        'consumer_first_name',
        'consumer_last_name',
        'consumer_document_type',
        'consumer_document_number',
        'consumer_phone',
        'consumer_email',
        'product_name',
        'order_number',
        'product_description',
        'claim_detail',
        'consumer_request',
        'status',
        'admin_response',
        'responded_at',
        'responded_by',
        'accepted_terms',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'accepted_terms' => 'boolean',
        'responded_at' => 'datetime',
    ];

    // ========= ACTIVITY LOG (Spatie) =========
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status',
                'admin_response',
                'responded_by',
                'responded_at',
                'claim_type',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Reclamo registrado',
                'updated' => 'Reclamo actualizado',
                'deleted' => 'Reclamo eliminado',
                default => "Reclamo {$eventName}",
            });
    }

    // ========= BOOT: correlativo automático =========
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($claim) {
            if (empty($claim->claim_number)) {
                $claim->claim_number = self::generateClaimNumber();
            }
        });
    }

    public static function generateClaimNumber(): string
    {
        $year = now()->year;
        $prefix = "LR-{$year}-";

        $last = self::where('claim_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('claim_number');

        $nextNumber = 1;
        if ($last) {
            $lastNumber = (int) substr($last, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    // ========= ACCESORES =========
    public function getFullNameAttribute(): string
    {
        return trim("{$this->consumer_first_name} {$this->consumer_last_name}");
    }

    public function getClaimTypeLabelAttribute(): string
    {
        return self::getTypeOptions()[$this->claim_type] ?? $this->claim_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Días hábiles (lun-vie) transcurridos desde el registro.
     * Nota: no excluye feriados. Si necesitas excluir feriados peruanos,
     * extiende este método con un calendario propio.
     */
    public function getBusinessDaysSinceCreatedAttribute(): int
    {
        if (!$this->created_at) {
            return 0;
        }
        return (int) $this->created_at->copy()->startOfDay()->diffInWeekdays(now()->startOfDay());
    }

    /**
     * Días hábiles restantes para responder (puede ser negativo si está vencido).
     */
    public function getBusinessDaysRemainingAttribute(): int
    {
        return self::LEGAL_DEADLINE_DAYS - $this->business_days_since_created;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->isOpen() && $this->business_days_remaining < 0;
    }

    public function getIsApproachingDeadlineAttribute(): bool
    {
        return $this->isOpen()
            && $this->business_days_since_created >= self::WARNING_DEADLINE_DAYS
            && $this->business_days_remaining >= 0;
    }

    // ========= ESTADOS HELPERS =========
    public function isOpen(): bool
    {
        return !in_array($this->status, [self::STATUS_ATENDIDO, self::STATUS_RECHAZADO]);
    }

    // ========= SCOPES =========
    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereNotIn('status', [self::STATUS_ATENDIDO, self::STATUS_RECHAZADO]);
    }

    public function scopeClosed(Builder $q): Builder
    {
        return $q->whereIn('status', [self::STATUS_ATENDIDO, self::STATUS_RECHAZADO]);
    }

    // ========= RELACIONES =========
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}