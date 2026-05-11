<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function __construct(
        protected ?Builder $query = null
    ) {
    }

    /**
     * Si recibimos una query filtrada de Filament la usamos; sino, replicamos
     * el criterio del listado: solo pagos APROBADOS, 1 fila por orden (último
     * approved por order_id).
     */
    public function query(): Builder
    {
        $base = $this->query ?? Payment::query()
            ->where('status', Payment::STATUS_APPROVED)
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('payments')
                    ->where('status', Payment::STATUS_APPROVED)
                    ->whereNull('deleted_at')
                    ->groupBy('order_id');
            });

        return $base->with([
            'order' => fn($q) => $q->withCount('payments')->with('user'),
        ]);
    }

    public function title(): string
    {
        return 'Pagos';
    }

    public function headings(): array
    {
        return [
            // === CLIENTE ===
            'Tipo Cliente',
            'Nombre',
            'Apellido',
            'Email',
            'Teléfono',

            // === ORDEN ===
            'N° Orden',
            'Estado Pedido',
            'Intentos de Pago',

            // === PAGO (último intento) ===
            'ID MercadoPago',
            'Estado Pago',
            'Método de Pago',
            'Monto',
            'Fecha del Pago',
            'Fecha de Aprobación',
            'Fecha de Rechazo',

            // === ORDEN: extras útiles para reportes ===
            'Total Orden',
            'Fecha de Orden',
        ];
    }

    public function map($payment): array
    {
        $order = $payment->order;

        // ===== Cliente: separar nombre/apellido =====
        $isGuest = is_null($order?->user_id);

        if ($isGuest) {
            $tipo = 'Invitado';
            $nombre = $order?->guest_name;
            $apellido = $order?->guest_last_name;
            $email = $order?->guest_email;
        } else {
            $tipo = 'Cliente';
            $fullName = trim((string) ($order?->user?->name ?? ''));
            $parts = explode(' ', $fullName, 2);
            $nombre = $parts[0] ?? '';
            $apellido = $parts[1] ?? '';
            $email = $order?->user?->email;
        }

        // ===== Estado pago legible =====
        $paymentStatusLabel = $payment->status
            ? (Payment::getStatusOptions()[$payment->status] ?? $payment->status)
            : 'Sin estado';

        return [
            // Cliente
            $tipo,
            $nombre,
            $apellido,
            $email,
            $order?->guest_phone,

            // Orden
            $order?->order_number,
            $order?->status_label,
            $order?->payments_count ?? 0,

            // Pago
            $payment->external_id,
            $paymentStatusLabel,
            $payment->payment_method,
            (float) ($payment->amount ?? 0),
            $payment->created_at?->format('d/m/Y H:i'),
            $payment->paid_at?->format('d/m/Y H:i'),
            $payment->failed_at?->format('d/m/Y H:i'),

            // Extras de la orden
            (float) ($order?->final_total ?? 0),
            $order?->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => '"S/ "#,##0.00',  // Monto pago
            'P' => '"S/ "#,##0.00',  // Total orden
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
                    'startColor' => ['rgb' => '1F2937'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}