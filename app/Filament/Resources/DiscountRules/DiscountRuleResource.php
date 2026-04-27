<?php

namespace App\Filament\Resources\DiscountRules;

use App\Filament\Resources\DiscountRules\Schemas\DiscountRuleForm;
use App\Filament\Resources\DiscountRules\Tables\DiscountRulesTable;
use App\Models\DiscountRule;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
class DiscountRuleResource extends Resource
{
    protected static ?string $model = DiscountRule::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración E-commerce';

    protected static ?string $navigationLabel = 'Descuentos Automáticos';
    protected static ?string $label = 'Regla de Descuento';
    protected static ?string $pluralLabel = 'Reglas de Descuento Automático';

    protected static ?int $navigationSort = 2;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return DiscountRuleForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return DiscountRulesTable::configure($table);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Administrador');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DiscountRules\Pages\ListDiscountRules::route('/'),
            'create' => \App\Filament\Resources\DiscountRules\Pages\CreateDiscountRule::route('/create'),
            'edit' => \App\Filament\Resources\DiscountRules\Pages\EditDiscountRule::route('/{record}/edit'),
        ];
    }
}