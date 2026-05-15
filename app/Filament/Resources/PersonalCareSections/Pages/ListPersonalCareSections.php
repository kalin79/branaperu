<?php
namespace App\Filament\Resources\PersonalCareSections\Pages;

use App\Filament\Resources\PersonalCareSections\PersonalCareSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPersonalCareSections extends ListRecords
{
    protected static string $resource = PersonalCareSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}