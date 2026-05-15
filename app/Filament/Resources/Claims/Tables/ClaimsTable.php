<?php

namespace App\Filament\Resources\Claims\Tables;

use App\Exports\ClaimsExport;
use App\Mail\ClaimResponded;
use App\Models\Claim;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('claim_number')
                    ->label('N°')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('claim_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => Claim::getTypeOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ($state) {
                        Claim::TYPE_RECLAMO => 'danger',
                        Claim::TYPE_QUEJA => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('full_name')
                    ->label('Consumidor')
                    ->searchable(['consumer_first_name', 'consumer_last_name'])
                    ->sortable(),

                TextColumn::make('consumer_document_number')
                    ->label('Documento')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('consumer_email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('consumer_phone')
                    ->label('Celular')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product_name')
                    ->label('Producto')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => Claim::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ($state) {
                        Claim::STATUS_PENDIENTE => 'warning',
                        Claim::STATUS_EN_REVISION => 'info',
                        Claim::STATUS_ATENDIDO => 'success',
                        Claim::STATUS_RECHAZADO => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('responded_at')
                    ->label('Respondido')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('respondedBy.name')
                    ->label('Atendido por')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')

            // ====================== FILTROS ======================
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Claim::getStatusOptions())
                    ->multiple(),

                SelectFilter::make('claim_type')
                    ->label('Tipo')
                    ->options(Claim::getTypeOptions())
                    ->multiple(),

                Filter::make('created_at')
                    ->label('Fecha de registro')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Desde'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['until'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])

            // ====================== ACCIONES POR FILA ======================
            ->actions([
                Action::make('respond')
                    ->label('Responder')
                    ->icon(Heroicon::ChatBubbleLeft)
                    ->color('success')
                    ->visible(fn(Claim $record) => $record->status !== Claim::STATUS_ATENDIDO)
                    ->schema([
                        Select::make('status')
                            ->label('Nuevo estado')
                            ->options(Claim::getStatusOptions())
                            ->default(Claim::STATUS_ATENDIDO)
                            ->required()
                            ->native(false),
                        Textarea::make('admin_response')
                            ->label('Respuesta al consumidor')
                            ->rows(8)
                            ->required()
                            ->minLength(10)
                            ->maxLength(5000)
                            ->placeholder('Estimado(a), agradecemos su comunicación...')
                            ->helperText('Esta respuesta se enviará automáticamente al correo del consumidor.'),
                    ])
                    ->modalHeading(fn(Claim $record) => 'Responder reclamo ' . $record->claim_number)
                    ->modalSubmitActionLabel('Guardar y enviar correo')
                    ->action(function (Claim $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'admin_response' => $data['admin_response'],
                            'responded_by' => auth()->id(),
                            'responded_at' => now(),
                        ]);

                        // Enviar correo al consumidor con la respuesta
                        try {
                            Mail::to($record->consumer_email)->send(new ClaimResponded($record));
                        } catch (\Throwable $e) {
                            Log::error('Error enviando respuesta de reclamo ' . $record->claim_number . ': ' . $e->getMessage());

                            Notification::make()
                                ->title('Respuesta guardada, pero el correo falló')
                                ->body('Revisa el log para más detalles.')
                                ->warning()
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Respuesta enviada al consumidor')
                            ->body($record->consumer_email)
                            ->success()
                            ->send();
                    }),

                EditAction::make()->label('Ver'),
            ])

            // ====================== BULK ACTIONS ======================
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('exportSelected')
                        ->label('Exportar selección a Excel')
                        ->icon(Heroicon::ArrowDownTray)
                        ->color('success')
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            $query = Claim::query()->whereIn('id', $ids);

                            return Excel::download(
                                new ClaimsExport($query),
                                'reclamos-seleccion-' . now()->format('Y-m-d_His') . '.xlsx'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}