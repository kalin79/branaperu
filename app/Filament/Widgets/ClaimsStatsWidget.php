<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClaimsStatsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Libro de Reclamaciones';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }

    protected function getStats(): array
    {
        // === Conteos por estado ===
        $pendientes = Claim::where('status', Claim::STATUS_PENDIENTE)->count();
        $enRevision = Claim::where('status', Claim::STATUS_EN_REVISION)->count();

        // Atendidos este mes
        $atendidosMes = Claim::where('status', Claim::STATUS_ATENDIDO)
            ->whereMonth('responded_at', now()->month)
            ->whereYear('responded_at', now()->year)
            ->count();

        // === Vencidos ===
        $openClaims = Claim::query()->open()->get();
        $vencidos = $openClaims->filter(fn(Claim $c) => $c->is_overdue)->count();

        // === Tiempo promedio de respuesta (en días hábiles) ===
        $respondidos = Claim::whereNotNull('responded_at')->get();
        if ($respondidos->isNotEmpty()) {
            $diasPromedio = $respondidos->avg(
                fn(Claim $c) => $c->created_at->copy()->startOfDay()->diffInWeekdays($c->responded_at->copy()->startOfDay())
            );
            $diasPromedio = round($diasPromedio, 1);
        } else {
            $diasPromedio = 0;
        }

        // === % cumplimiento del plazo legal ===
        if ($respondidos->isNotEmpty()) {
            $aTiempo = $respondidos->filter(function (Claim $c) {
                $dias = $c->created_at->copy()->startOfDay()->diffInWeekdays($c->responded_at->copy()->startOfDay());
                return $dias <= Claim::LEGAL_DEADLINE_DAYS;
            })->count();
            $cumplimiento = round(($aTiempo / $respondidos->count()) * 100, 1);
        } else {
            $cumplimiento = 100;
        }

        return [
            Stat::make('Pendientes', $pendientes)
                ->description('Sin atender aún')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendientes > 0 ? 'warning' : 'success'),

            Stat::make('En revisión', $enRevision)
                ->description('Trabajándose')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            Stat::make('Vencidos', $vencidos)
                ->description($vencidos > 0 ? '¡Atender urgente!' : 'Ninguno vencido')
                ->descriptionIcon($vencidos > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($vencidos > 0 ? 'danger' : 'success'),

            Stat::make('Atendidos este mes', $atendidosMes)
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Tiempo prom. respuesta', $diasPromedio . ' días háb.')
                ->description('Histórico')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($diasPromedio <= Claim::LEGAL_DEADLINE_DAYS ? 'success' : 'danger'),

            Stat::make('Cumplimiento legal', $cumplimiento . '%')
                ->description('Respondidos dentro de 15 días háb.')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($cumplimiento >= 95 ? 'success' : ($cumplimiento >= 80 ? 'warning' : 'danger')),
        ];
    }
}