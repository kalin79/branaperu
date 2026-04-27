<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Helper para saber si es administrador
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }
    // Agrega esto dentro de la clase User
    public function canAccessFilament(): bool
    {
        return $this->hasAnyRole(['Administrador', 'Editor']);
    }
}