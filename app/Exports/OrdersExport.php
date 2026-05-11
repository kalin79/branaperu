<?php

namespace App\Exports;

use App\Models\Order;
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

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function __construct(
        protected ?Builder $query = null
    ) {
    }

    /**
     * Query base con eager loading para evitar N+1.
     * Si recibimos una query filtrada de Filament la usamos; sino, todas las órdenes.
     */
    public function query(): Builder
    {
        $base = $this->query ?? Order::query();

        return $base->with(['user', 'coupon', 'district', 'currentPayment']);
    }

    public function title(): string
    {
        return 'Órdenes';
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
            'DNI',

            // === ORDEN ===
            'N° Orden',
            'Fecha',
            'Estado Pedido',
            'Estado Pago',
            'Método de Pago',

            // === MONTOS ===
            'Subtotal',
            'Descuento Total',
            'Costo Envío',
            'Total Final',

            // === CUPÓN ===
            'Cupón - Código',
            'Cupón - Nombre',
            'Cupón - Tipo',
            'Cupón - Valor Nominal',

            // === DESCUENTO AUTOMÁTICO ===
            'Descuento Auto. - Nombre',
            'Descuento Auto. - %',

            // === ENTREGA ===
            'Método de Entrega',
            'Distrito / Local',
            'Dirección',

            // === DOCUMENTO ===
            'Tipo Documento',
            'RUC',
            'Razón Social',
        ];
    }

    public function map($order): array
    {
        // ===== Cliente: separar nombre/apellido =====
        $isGuest = is_null($order->user_id);

        if ($isGuest) {
            $tipo = 'Invitado';
            $nombre = $order->guest_name;
            $apellido = $order->guest_last_name;
            $email = $order->guest_email;
        } else {
            $tipo = 'Cliente';
            // user->name suele ser "Juan Pérez" → partir en primer y resto
            $fullName = trim((string) ($order->user?->name ?? ''));
            $parts = explode(' ', $fullName, 2);
            $nombre = $parts[0] ?? '';
            $apellido = $parts[1] ?? '';
            $email = $order->user?->email;
        }

        // ===== Cupón: tipo legible =====
        $couponType = match ($order->coupon?->discount_type) {
            'percent' => 'Porcentaje',
            'fixed' => 'Monto Fijo',
            default => null,
        };

        // ===== Estado pago legible =====
        $paymentStatus = $order->currentPayment?->status;
        $paymentStatusLabel = $paymentStatus
            ? (Payment::getStatusOptions()[$paymentStatus] ?? $paymentStatus)
            : 'Sin pago';

        // ===== Entrega =====
        $isPickup = $order->isPickup();
        $deliveryMethodLabel = $isPickup ? 'Retiro en Tienda' : 'Envío a Domicilio';
        $deliveryLocation = $isPickup
            ? $order->pickup_local_name
            : $order->district?->full_name;
        $direccion = $isPickup
            ? $order->pickup_local_address
            : $order->shipping_address;

        // ===== Documento =====
        $docType = match ($order->document_type) {
            Order::DOCUMENT_TYPE_FACTURA => 'Factura',
            Order::DOCUMENT_TYPE_BOLETA => 'Boleta',
            default => null,
        };

        return [
            // Cliente
            $tipo,
            $nombre,
            $apellido,
            $email,
            $order->guest_phone,
            $order->dni,

            // Orden
            $order->order_number,
            $order->created_at?->format('d/m/Y H:i'),
            $order->status_label,
            $paymentStatusLabel,
            $order->currentPayment?->payment_method,

            // Montos (numéricos para que Excel los sume correctamente)
            (float) ($order->subtotal ?? 0),
            (float) ($order->discount_amount ?? 0),
            (float) ($order->delivery_cost ?? 0),
            (float) ($order->final_total ?? 0),

            // Cupón
            $order->coupon_code,
            $order->coupon_name,
            $couponType,
            $order->coupon_discount_value,

            // Descuento automático
            $order->discount_rule_name,
            $order->discount_rule_percent,

            // Entrega
            $deliveryMethodLabel,
            $deliveryLocation,
            $direccion,

            // Documento
            $docType,
            $order->billing_ruc,
            $order->billing_business_name,
        ];
    }

    /**
     * Formato de celdas: columnas de dinero como moneda PEN.
     */
    public function columnFormats(): array
    {
        return [
            'L' => '"S/ "#,##0.00',  // Subtotal
            'M' => '"S/ "#,##0.00',  // Descuento Total
            'N' => '"S/ "#,##0.00',  // Costo Envío
            'O' => '"S/ "#,##0.00',  // Total Final
            'T' => '0.00"%"',         // Descuento Auto - %
        ];
    }

    /**
     * Estilo del encabezado (fila 1): negrita, fondo oscuro, texto blanco.
     */
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