<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas - Ecommerce';

    protected static ?string $navigationLabel = 'Todas las Ventas';
    protected static ?string $label = 'Venta';
    protected static ?string $pluralLabel = 'Ventas';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return OrdersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Orders\Pages\ListOrders::route('/'),
            'pending' => \App\Filament\Resources\Orders\Pages\ListPendingOrders::route('/pending'),
            'successful' => \App\Filament\Resources\Orders\Pages\ListSuccessfulOrders::route('/successful'),
            'view' => \App\Filament\Resources\Orders\Pages\ViewOrder::route('/{record}'),
        ];
    }

    /**
     * Menú lateral organizado bajo "Ventas - Ecommerce"
     */
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Todas las Ventas')
                ->icon(Heroicon::ShoppingCart)
                ->url(static::getUrl('index'))
                ->group('Ventas - Ecommerce')
                ->sort(1),

            NavigationItem::make('Ventas Pendientes')
                ->icon(Heroicon::Clock)
                ->url(static::getUrl('pending'))
                ->group('Ventas - Ecommerce')
                ->sort(2),

            NavigationItem::make('Ventas Exitosas')
                ->icon(Heroicon::CheckCircle)
                ->url(static::getUrl('successful'))
                ->group('Ventas - Ecommerce')
                ->sort(3),
        ];
    }
}