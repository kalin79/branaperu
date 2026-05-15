<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\CreateBanner;
use App\Filament\Resources\Banners\Pages\EditBanner;
use App\Filament\Resources\Banners\Pages\ListBanners;
use App\Filament\Resources\Banners\Schemas\BannerForm;
use App\Filament\Resources\Banners\Tables\BannersTable;
use App\Models\Banner;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Photo;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Banners';
    protected static ?string $label = 'Banner';
    protected static ?string $pluralLabel = 'Banners';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return BannerForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return BannersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit'   => EditBanner::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Administrador', 'Editor']);
    }
}
