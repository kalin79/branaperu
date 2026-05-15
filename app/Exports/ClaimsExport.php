<?php

namespace App\Exports;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClaimsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function __construct(
        protected ?Builder $query = null
    ) {
    }

    public function query(): Builder
    {
        $base = $this->query ?? Claim::query();
        return $base->with(['respondedBy']);
    }

    public function title(): string
    {
        return 'Libro de Reclamaciones';
    }

    public function headings(): array
    {
        return [
            // Identificación del reclamo
            'N° Reclamo',
            'Tipo',
            'Estado',
            'Fecha de registro',

            // Consumidor
            'Nombres',
            'Apellidos',
            'Tipo Doc.',
            'N° Documento',
            'Celular',
            'Correo',

            // Bien
            'Producto',
            'N° de Pedido',
            'Descripción del bien',

            // Detalle
            'Detalle del reclamo',
            'Pedido del consumidor',

            // Respuesta
            'Respuesta del proveedor',
            'Atendido por',
            'Fecha de respuesta',

            // Auditoría
            'IP',
            'Aceptó términos',
            'Última actualización',
        ];
    }

    public function map($claim): array
    {
        return [
            // Identificación
            $claim->claim_number,
            $claim->claim_type_label,
            $claim->status_label,
            $claim->created_at?->format('d/m/Y H:i'),

            // Consumidor
            $claim->consumer_first_name,
            $claim->consumer_last_name,
            $claim->consumer_document_type,
            $claim->consumer_document_number,
            $claim->consumer_phone,
            $claim->consumer_email,

            // Bien
            $claim->product_name,
            $claim->order_number,
            $claim->product_description,

            // Detalle
            $claim->claim_detail,
            $claim->consumer_request,

            // Respuesta
            $claim->admin_response,
            $claim->respondedBy?->name,
            $claim->responded_at?->format('d/m/Y H:i'),

            // Auditoría
            $claim->ip_address,
            $claim->accepted_terms ? 'Sí' : 'No',
            $claim->updated_at?->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B5E20'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}