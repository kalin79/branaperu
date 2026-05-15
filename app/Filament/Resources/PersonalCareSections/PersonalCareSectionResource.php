<?php
namespace App\Filament\Resources\PersonalCareSections;

use App\Filament\Resources\PersonalCareSections\Pages\CreatePersonalCareSection;
use App\Filament\Resources\PersonalCareSections\Pages\EditPersonalCareSection;
use App\Filament\Resources\PersonalCareSections\Pages\ListPersonalCareSections;
use App\Filament\Resources\PersonalCareSections\Pages\ManageFeatures;
use App\Filament\Resources\PersonalCareSections\Schemas\PersonalCareSectionForm;
use App\Filament\Resources\PersonalCareSections\Tables\PersonalCareSectionsTable;
use App\Models\PersonalCareSection;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PersonalCareSectionResource extends Resource
{
    protected static ?string $model = PersonalCareSection::class;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Home;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Cuidado Personal';
    protected static ?string $label = 'Sección';
    protected static ?string $pluralLabel = 'Cuidado Personal';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return PersonalCareSectionForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return PersonalCareSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\PersonalCareSections\RelationManagers\FeaturesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPersonalCareSections::route('/'),
            'create' => CreatePersonalCareSection::route('/create'),
            'edit' => EditPersonalCareSection::route('/{record}/edit'),
            'manage-features' => ManageFeatures::route('/{record}/features'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }
}