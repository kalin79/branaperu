<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn(string $operation) => $operation === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Rol en el Panel')
                    ->schema([
                        Select::make('roles')
                            ->label('Rol')
                            ->relationship('roles', 'name')
                            ->options(Role::pluck('name', 'id'))   // ← Clave: usamos ID como valor
                            ->multiple(false)                       // Solo permite 1 rol
                            ->preload()
                            ->required()
                            ->helperText('Administrador = acceso total | Editor = solo productos, categorías, cupones y ventas'),
                    ]),
            ]);
    }
}