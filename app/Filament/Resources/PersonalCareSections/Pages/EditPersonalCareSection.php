<?php
namespace App\Filament\Resources\PersonalCareSections\Pages;

use App\Filament\Resources\PersonalCareSections\PersonalCareSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPersonalCareSection extends EditRecord
{
    protected static string $resource = PersonalCareSectionResource::class;

    protected string $view = 'filament.resources.personal-care-sections.pages.edit-personal-care-section';

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getViewData(): array
    {
        return ['record' => $this->record];
    }
}