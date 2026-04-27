<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración Usuarios';

    protected static ?string $navigationLabel = 'Usuarios del Panel';
    protected static ?string $label = 'Usuario';
    protected static ?string $pluralLabel = 'Usuarios';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return UsersTable::configure($table);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Administrador');
    }


    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Users\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\Users\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\Users\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}