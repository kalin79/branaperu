<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ApprovedOrdersChart extends ChartWidget
{
    protected ?string $heading = 'Ventas aprobadas';

    protected ?string $description = 'Órdenes con pago aprobado';

    // Ocupa todo el ancho del dashboard
    protected int|string|array $columnSpan = 'full';

    // Orden en el dashboard (más bajo = aparece antes)
    protected static ?int $sort = 2;

    /**
     * Filtros disponibles arriba del gráfico
     */
    protected function getFilters(): ?array
    {
        return [
            'last_30' => 'Últimos 30 días',
            'last_90' => 'Últimos 3 meses',
            'last_180' => 'Últimos 6 meses',
        ];
    }

    public ?string $filter = 'last_90';

    protected function getData(): array
    {
        // Determina rango según filtro
        $days = match ($this->filter) {
            'last_30' => 30,
            'last_180' => 180,
            default => 90,
        };

        $start = now()->subDays($days - 1)->startOfDay();
        $end = now()->endOfDay();

        // Trae las órdenes aprobadas agrupadas por día de pago
        //
        // IMPORTANTE: usamos DB::table('orders') (query builder "crudo") en lugar
        // de Order::query() porque el modelo Order tiene un accessor
        // getTotalAttribute() que intercepta cualquier propiedad llamada `total`
        // al hidratarse como modelo y devuelve final_total casteado a float.
        // Eso "pisaba" el COUNT(...) as total y dejaba la serie de órdenes en 0.
        //
        // Como segunda capa de seguridad, además renombramos los alias a
        // `orders_count` e `income_total` para que jamás colisionen con
        // accessors / casts del modelo si en el futuro alguien vuelve a usar
        // Eloquent aquí.
        $rows = DB::table('orders')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', Payment::STATUS_APPROVED)
            ->whereNull('orders.deleted_at') // respetar SoftDeletes manualmente
            ->whereBetween('payments.paid_at', [$start, $end])
            ->select(
                DB::raw('DATE(payments.paid_at) as day'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(orders.final_total) as income_total'),
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Construye una serie diaria continua (rellena días sin ventas con 0)
        $labels = [];
        $orders = [];
        $income = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $key = $date->format('Y-m-d');
            $row = $rows->get($key);

            $labels[] = $date->translatedFormat('d M');
            $orders[] = (int) ($row?->orders_count ?? 0);
            $income[] = (float) ($row?->income_total ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Órdenes aprobadas',
                    'data' => $orders,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'tension' => 0.3,
                    'fill' => true,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Ingresos (S/)',
                    'data' => $income,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'tension' => 0.3,
                    'fill' => false,
                    'yAxisID' => 'y1',
                    'hidden' => true, // empieza oculta; el usuario la activa con el click en la leyenda
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Opciones extra de Chart.js (eje secundario para ingresos)
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'N° de órdenes',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'beginAtZero' => true,
                    'grid' => ['drawOnChartArea' => false],
                    'title' => [
                        'display' => true,
                        'text' => 'S/ Ingresos',
                    ],
                ],
            ],
        ];
    }
}