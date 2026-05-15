<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Widgets\ChartWidget;

class ClaimsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Reclamos por mes (últimos 6 meses)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }

    protected function getData(): array
    {
        $labels = [];
        $reclamos = [];
        $quejas = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = ucfirst($month->locale('es')->isoFormat('MMM YYYY'));

            $reclamos[] = Claim::where('claim_type', Claim::TYPE_RECLAMO)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $quejas[] = Claim::where('claim_type', Claim::TYPE_QUEJA)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Reclamos',
                    'data' => $reclamos,
                    'backgroundColor' => 'rgba(183, 28, 28, 0.7)',
                    'borderColor' => 'rgba(183, 28, 28, 1)',
                ],
                [
                    'label' => 'Quejas',
                    'data' => $quejas,
                    'backgroundColor' => 'rgba(245, 124, 0, 0.7)',
                    'borderColor' => 'rgba(245, 124, 0, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}