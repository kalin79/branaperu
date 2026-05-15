<?php

namespace App\Filament\Resources\Claims;

use App\Filament\Resources\Claims\Pages\EditClaim;
use App\Filament\Resources\Claims\Pages\ListClaims;
use App\Filament\Resources\Claims\Schemas\ClaimForm;
use App\Filament\Resources\Claims\Tables\ClaimsTable;
use App\Models\Claim;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftEllipsis;
    protected static string|UnitEnum|null $navigationGroup = 'Reclamos';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Libro de Reclamaciones';

    protected static ?string $label = 'Reclamo';
    protected static ?string $pluralLabel = 'Reclamos';
    protected static ?string $recordTitleAttribute = 'claim_number';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return ClaimForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return ClaimsTable::configure($table);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }

    /**
     * Badge en el menú: número de reclamos pendientes.
     */
    public static function getNavigationBadge(): ?string
    {
        $pending = Claim::where('status', Claim::STATUS_PENDIENTE)->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClaims::route('/'),
            'edit' => EditClaim::route('/{record}/edit'),
        ];
    }
}