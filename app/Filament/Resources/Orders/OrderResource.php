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

    protected static string|UnitEnum|null $navigationGroup = 'Order - Ecommerce';

    protected static ?string $navigationLabel = 'Todas las Ordenes de Venta';
    protected static ?string $label = 'Order';
    protected static ?string $pluralLabel = 'Ordenes';

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
            'abandoned' => \App\Filament\Resources\Orders\Pages\AbandonedOrders::route('/abandoned'),
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
            NavigationItem::make('Todas las Ordenes')
                ->icon(Heroicon::ShoppingCart)
                ->url(static::getUrl('index'))
                ->group('Ventas - Ecommerce')
                ->sort(1),
            NavigationItem::make('Carritos Abandonados')
                ->icon(Heroicon::Clock)
                ->url(static::getUrl('abandoned'))
                ->group('Ventas - Ecommerce')
                ->sort(2),

            // NavigationItem::make('Ventas Pendientes')
            //     ->icon(Heroicon::Clock)
            //     ->url(static::getUrl('pending'))
            //     ->group('Ventas - Ecommerce')
            //     ->sort(2),

            // NavigationItem::make('Ventas Exitosas')
            //     ->icon(Heroicon::CheckCircle)
            //     ->url(static::getUrl('successful'))
            //     ->group('Ventas - Ecommerce')
            //     ->sort(3),
        ];
    }
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Administrador');
    }
}