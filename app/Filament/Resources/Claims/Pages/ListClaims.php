<?php

namespace App\Filament\Resources\Claims\Pages;

use App\Exports\ClaimsExport;
use App\Filament\Resources\Claims\ClaimResource;
use App\Models\Claim;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class ListClaims extends ListRecords
{
    protected static string $resource = ClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportAll')
                ->label('Exportar todo a Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->color('success')
                ->action(function () {
                    // Respeta los filtros y búsqueda aplicados en pantalla
                    $query = $this->getFilteredTableQuery();

                    return Excel::download(
                        new ClaimsExport($query),
                        'reclamos-' . now()->format('Y-m-d_His') . '.xlsx'
                    );
                }),
        ];
    }
}