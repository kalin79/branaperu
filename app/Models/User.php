<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'document_type',
        'document_number',
        'department',
        'province',
        'district_id',
        'status',           // Nuevo: Activo / Bloqueado
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'string',
    ];

    // ====================== ESTADOS ======================
    const STATUS_ACTIVE = 'activo';
    const STATUS_BLOCKED = 'bloqueado';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Activo',
            self::STATUS_BLOCKED => 'Bloqueado',
        ];
    }

    // ====================== RELACIONES ======================
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    // Helper para saber si es administrador
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }
    public function isEditor(): bool
    {
        return $this->hasRole('Editor');
    }
    public function isClient(): bool
    {
        return $this->hasRole('Cliente');
    }
    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }
    // Agrega esto dentro de la clase User
    public function canAccessFilament(): bool
    {
        return $this->hasAnyRole(['Administrador', 'Editor']);
    }
}