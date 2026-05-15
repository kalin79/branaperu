<?php

namespace App\Filament\Resources\Claims\Schemas;

use App\Models\Claim;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ClaimForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ============= CABECERA =============
            Section::make('Información del reclamo')
                ->schema([
                    Placeholder::make('claim_number')
                        ->label('N° de Reclamo')
                        ->content(fn($record) => new HtmlString(
                            '<span style="font-weight:700;font-size:18px;color:#1b5e20;">'
                            . e($record?->claim_number ?? '—')
                            . '</span>'
                        )),

                    Placeholder::make('created_at')
                        ->label('Fecha de registro')
                        ->content(fn($record) => $record?->created_at?->format('d/m/Y H:i') ?? '—'),

                    Placeholder::make('claim_type_label')
                        ->label('Tipo')
                        ->content(fn($record) => $record?->claim_type_label ?? '—'),

                    Placeholder::make('deadline')
                        ->label('Plazo legal')
                        ->content(function ($record) {
                            if (!$record) {
                                return '—';
                            }
                            if (!$record->isOpen()) {
                                return new HtmlString(
                                    '<span style="color:#1b5e20;font-weight:600;">Cerrado</span>'
                                );
                            }
                            $rem = $record->business_days_remaining;
                            if ($rem < 0) {
                                return new HtmlString(
                                    '<span style="color:#b71c1c;font-weight:700;">Vencido por ' . abs($rem) . ' día(s) hábiles</span>'
                                );
                            }
                            $color = $rem <= 2 ? '#e65100' : '#1b5e20';
                            return new HtmlString(
                                '<span style="color:' . $color . ';font-weight:600;">' . $rem . ' día(s) hábiles restante(s)</span>'
                            );
                        }),
                ])
                ->columns(4)
                ->columnSpanFull(),

            // ============= CONSUMIDOR =============
            Section::make('Identificación del consumidor')
                ->schema([
                    TextInput::make('consumer_first_name')->label('Nombres')->disabled()->dehydrated(false),
                    TextInput::make('consumer_last_name')->label('Apellidos')->disabled()->dehydrated(false),
                    TextInput::make('consumer_document_type')->label('Tipo de documento')->disabled()->dehydrated(false),
                    TextInput::make('consumer_document_number')->label('N° de documento')->disabled()->dehydrated(false),
                    TextInput::make('consumer_phone')->label('Celular')->disabled()->dehydrated(false),
                    TextInput::make('consumer_email')->label('Correo')->disabled()->dehydrated(false),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->collapsible(),

            // ============= BIEN CONTRATADO =============
            Section::make('Bien / servicio')
                ->schema([
                    TextInput::make('product_name')->label('Producto')->disabled()->dehydrated(false),
                    TextInput::make('order_number')->label('N° de pedido')->disabled()->dehydrated(false),
                    Textarea::make('product_description')->label('Descripción')->rows(3)->disabled()->dehydrated(false)->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->collapsible(),

            // ============= DETALLE DEL RECLAMO =============
            Section::make('Detalle del consumidor')
                ->schema([
                    Textarea::make('claim_detail')->label('Detalle')->rows(5)->disabled()->dehydrated(false),
                    Textarea::make('consumer_request')->label('Pedido del consumidor')->rows(4)->disabled()->dehydrated(false),
                ])
                ->columnSpanFull(),

            // ============= RESPUESTA DEL ADMIN =============
            Section::make('Respuesta del proveedor')
                ->description('Esta respuesta se enviará al correo del consumidor cuando guardes los cambios y el estado sea "Atendido".')
                ->schema([
                    Select::make('status')
                        ->label('Estado')
                        ->options(Claim::getStatusOptions())
                        ->required()
                        ->native(false),
                    Textarea::make('admin_response')
                        ->label('Respuesta al consumidor')
                        ->rows(8)
                        ->maxLength(5000)
                        ->placeholder('Estimado(a), agradecemos su comunicación...')
                        ->helperText('Recuerda: el plazo máximo legal es de 15 días hábiles.')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            // ============= HISTORIAL DE CAMBIOS =============
            Section::make('Historial de cambios')
                ->description('Registro de auditoría — quién cambió qué y cuándo.')
                ->schema([
                    Placeholder::make('activity_log')
                        ->label('')
                        ->content(function ($record) {
                            if (!$record) {
                                return new HtmlString('<em style="color:#888;">Aún no hay historial.</em>');
                            }

                            $activities = $record->activities()->with('causer')->latest()->limit(50)->get();

                            if ($activities->isEmpty()) {
                                return new HtmlString('<em style="color:#888;">Sin cambios registrados todavía.</em>');
                            }

                            $html = '<div style="font-size:13px;max-height:420px;overflow-y:auto;">';
                            foreach ($activities as $a) {
                                $html .= '<div style="border-left:3px solid #2e7d32;padding:8px 12px;margin-bottom:10px;background:#fafafa;border-radius:0 4px 4px 0;">';
                                $html .= '<div style="display:flex;justify-content:space-between;align-items:center;">';
                                $html .= '<strong>' . e($a->description) . '</strong>';
                                $html .= '<small style="color:#777;">' . $a->created_at->format('d/m/Y H:i') . '</small>';
                                $html .= '</div>';

                                if ($a->causer) {
                                    $html .= '<div style="color:#555;font-size:12px;margin-top:2px;">por <strong>' . e($a->causer->name) . '</strong></div>';
                                } else {
                                    $html .= '<div style="color:#555;font-size:12px;margin-top:2px;">por <strong>sistema</strong></div>';
                                }

                                $attrs = $a->properties['attributes'] ?? [];
                                $old = $a->properties['old'] ?? [];

                                if (!empty($attrs)) {
                                    $html .= '<div style="margin-top:6px;font-size:12px;color:#444;">';
                                    foreach ($attrs as $key => $val) {
                                        $oldVal = $old[$key] ?? '—';
                                        $valStr = is_scalar($val) ? (string) $val : json_encode($val);
                                        $oldStr = is_scalar($oldVal) ? (string) $oldVal : json_encode($oldVal);
                                        $html .= '<div style="margin-bottom:2px;"><code style="background:#fff;padding:1px 4px;border:1px solid #ddd;border-radius:3px;">' . e($key) . '</code>: ';
                                        $html .= '<span style="color:#b71c1c;">' . e(\Illuminate\Support\Str::limit($oldStr, 80)) . '</span> → ';
                                        $html .= '<span style="color:#1b5e20;">' . e(\Illuminate\Support\Str::limit($valStr, 80)) . '</span></div>';
                                    }
                                    $html .= '</div>';
                                }

                                $html .= '</div>';
                            }
                            $html .= '</div>';

                            return new HtmlString($html);
                        }),
                ])
                ->collapsible()
                ->collapsed()
                ->columnSpanFull(),
        ]);
    }
}