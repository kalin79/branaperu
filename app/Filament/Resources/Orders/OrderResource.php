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

    // Nota: cuando definimos getNavigationItems() abajo, este `$navigationGroup`
    // ya no se usa para construir el menú. Se mantiene solo como referencia.
    protected static string|UnitEnum|null $navigationGroup = 'Ventas - Ecommerce';

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
     * Menú lateral organizado bajo "Ventas - Ecommerce".
     *
     * Cada NavigationItem necesita `isActiveWhen()` para que Filament sepa
     * cuándo "pintar" el item como activo (highlight) según la ruta actual.
     */
    public static function getNavigationItems(): array
    {
        // Nombre base de las rutas del recurso (ej: "filament.admin.resources.orders")
        $routeBase = static::getRouteBaseName();

        return [
            NavigationItem::make('Todas las Ordenes')
                ->icon(Heroicon::ShoppingCart)
                ->url(static::getUrl('index'))
                ->isActiveWhen(fn(): bool => request()->routeIs(
                    $routeBase . '.index',
                    $routeBase . '.view',          // resalta también al ver una orden
                ))
                ->group('Ventas - Ecommerce')
                ->sort(1),

            NavigationItem::make('Carritos Abandonados')
                ->icon(Heroicon::Clock)
                ->url(static::getUrl('abandoned'))
                ->isActiveWhen(fn(): bool => request()->routeIs($routeBase . '.abandoned'))
                ->group('Ventas - Ecommerce')
                ->sort(2),

            // NavigationItem::make('Ventas Pendientes')
            //     ->icon(Heroicon::Clock)
            //     ->url(static::getUrl('pending'))
            //     ->isActiveWhen(fn (): bool => request()->routeIs($routeBase . '.pending'))
            //     ->group('Ventas - Ecommerce')
            //     ->sort(3),

            // NavigationItem::make('Ventas Exitosas')
            //     ->icon(Heroicon::CheckCircle)
            //     ->url(static::getUrl('successful'))
            //     ->isActiveWhen(fn (): bool => request()->routeIs($routeBase . '.successful'))
            //     ->group('Ventas - Ecommerce')
            //     ->sort(4),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Administrador');
    }
}