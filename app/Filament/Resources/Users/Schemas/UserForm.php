<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;
use App\Models\District;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos Personales')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombres')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Apellidos')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        Select::make('document_type')
                            ->label('Tipo de documento')
                            ->options([
                                'DNI' => 'DNI',
                                'CE' => 'Carnet de Extranjería',
                                'Pasaporte' => 'Pasaporte',
                            ]),

                        TextInput::make('document_number')
                            ->label('Número de documento')
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                // ==================== UBICACIÓN ANIDADA ====================
                Section::make('Ubicación')
                    ->schema([
                        Select::make('department')
                            ->label('Departamento')
                            ->options(fn() => District::distinct()->orderBy('department')->pluck('department', 'department'))
                            ->live()
                            ->required()
                            ->afterStateUpdated(fn(callable $set) => $set('province', null)),

                        Select::make('province')
                            ->label('Provincia')
                            ->options(function (callable $get) {
                                $department = $get('department');
                                if (!$department)
                                    return [];
                                return District::where('department', $department)
                                    ->distinct()
                                    ->orderBy('province')
                                    ->pluck('province', 'province');
                            })
                            ->live()
                            ->required()
                            ->afterStateUpdated(fn(callable $set) => $set('district_id', null)),

                        Select::make('district_id')
                            ->label('Distrito')
                            ->options(function (callable $get) {
                                $department = $get('department');
                                $province = $get('province');
                                if (!$department || !$province)
                                    return [];
                                return District::where('department', $department)
                                    ->where('province', $province)
                                    ->pluck('district', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('address')
                            ->label('Dirección completa')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('address_reference')
                            ->label('Referencia de la dirección')
                            ->placeholder('Ej: frente al parque, puerta roja, al lado de la farmacia, 2do piso, etc.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Acceso y Estado')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'activo' => 'Activo',
                                'bloqueado' => 'Bloqueado',
                            ])
                            ->default('activo')
                            ->required(),

                        Select::make('roles')
                            ->label('Rol')
                            ->relationship('roles', 'name')
                            ->options(Role::pluck('name', 'id'))
                            ->multiple(false)
                            ->preload()
                            ->required()
                            ->helperText('Administrador = acceso total | Editor = limitado | Cliente = usuario de la web'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required(fn(string $operation) => $operation === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
            ]);
    }
}