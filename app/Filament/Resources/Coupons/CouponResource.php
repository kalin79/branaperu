<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Schemas\CouponForm;
use App\Filament\Resources\Coupons\Tables\CouponsTable;
use App\Models\Coupon;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Ticket;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración E-commerce';

    protected static ?string $navigationLabel = 'Cupones de Descuento';
    protected static ?string $label = 'Cupón';
    protected static ?string $pluralLabel = 'Cupones';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return CouponForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return CouponsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Coupons\Pages\ListCoupons::route('/'),
            'create' => \App\Filament\Resources\Coupons\Pages\CreateCoupon::route('/create'),
            'edit' => \App\Filament\Resources\Coupons\Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}