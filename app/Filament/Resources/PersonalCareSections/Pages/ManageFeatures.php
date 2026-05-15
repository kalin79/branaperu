<?php
namespace App\Filament\Resources\PersonalCareSections\Pages;

use App\Filament\Resources\PersonalCareSections\PersonalCareSectionResource;
use App\Filament\Resources\PersonalCareSections\RelationManagers\FeaturesRelationManager;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageFeatures extends ManageRelatedRecords
{
    protected static string $resource = PersonalCareSectionResource::class;
    protected static string $relationship = 'features';
    protected static ?string $title = 'Características';

    public function getRelationManagers(): array
    {
        return [FeaturesRelationManager::class];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Volver a la Sección')
                ->url(fn() => PersonalCareSectionResource::getUrl('edit', ['record' => $this->getRecord()]))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}