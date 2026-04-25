<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Product;

class RelatedProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'relatedProducts';

    protected static ?string $title = 'Productos Relacionados';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('related_product_id')
                    ->label('Producto Relacionado')
                    ->options(function () {
                        return Product::where('is_active', true)
                            ->where('id', '!=', $this->ownerRecord?->id ?? 0)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Sin productos relacionados')
            ->emptyStateDescription('Haz clic en "Agregar Producto Relacionado" para comenzar')
            ->emptyStateIcon('heroicon-o-link')
            ->columns([
                Tables\Columns\TextColumn::make('pivot.sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->default(0)
                    ->badge(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('pivot.is_active')
                    ->label('Activo'),
            ])
            ->defaultSort('pivot_sort_order')   // ← Alias interno de Filament v5
            ->headerActions([
                Action::make('attach')
                    ->label('Agregar Producto Relacionado')
                    ->icon('heroicon-o-plus')
                    ->form(fn() => $this->form(Schema::make())->getComponents())
                    ->action(function (array $data) {
                        $this->ownerRecord->relatedProducts()->attach($data['related_product_id'], [
                            'sort_order' => $data['sort_order'] ?? 0,
                            'is_active' => $data['is_active'] ?? true,
                        ]);
                    })
                    ->successNotificationTitle('Producto relacionado agregado'),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}