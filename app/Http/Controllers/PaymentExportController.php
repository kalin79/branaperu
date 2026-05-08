<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class PaymentExportController extends Controller
{
    public function export(): StreamedResponse
    {
        $data = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', Payment::STATUS_APPROVED)
            ->select(
                'orders.order_number',
                'orders.status as estado_pedido_raw',           // ← Usamos la columna real
                DB::raw('COUNT(payments.id) as numero_intentos'),
                DB::raw('MAX(payments.external_id) as ultimo_id_mercadopago'),
                DB::raw('MAX(payments.amount) as monto'),
                DB::raw('MAX(payments.payment_method) as metodo'),
                DB::raw('MAX(payments.paid_at) as fecha_pago'),
                DB::raw('MAX(payments.created_at) as fecha_creacion')
            )
            ->groupBy('orders.order_number', 'orders.status')
            ->orderBy('orders.order_number')
            ->get();

        $filename = 'ventas_aprobadas_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'N° Orden',
                'Estado del Pedido',
                'N° de Intentos',
                'Último ID MercadoPago',
                'Monto (S/)',
                'Método',
                'Fecha de Pago',
                'Fecha de Creación'
            ], ';');

            foreach ($data as $row) {
                // Convertimos el estado crudo a nombre legible
                $estadoPedido = match ($row->estado_pedido_raw) {
                    'pending' => 'Pendiente',
                    'abandoned' => 'Carrito Abandonado',
                    'paid' => 'Pagado',
                    'preparing' => 'Preparando',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregado',
                    'cancelled' => 'Cancelado',
                    'refunded' => 'Reembolsado',
                    'returned' => 'Devuelto',
                    default => ucfirst($row->estado_pedido_raw ?? 'Desconocido'),
                };

                $montoFormateado = 'S/. ' . number_format($row->monto ?? 0, 2, ',', '.');

                fputcsv($file, [
                    $row->order_number,
                    $estadoPedido,
                    $row->numero_intentos,
                    $row->ultimo_id_mercadopago,
                    $montoFormateado,
                    $row->metodo ?? '—',
                    $row->fecha_pago ? \Carbon\Carbon::parse($row->fecha_pago)->format('d/m/Y H:i') : '—',
                    $row->fecha_creacion ? \Carbon\Carbon::parse($row->fecha_creacion)->format('d/m/Y H:i') : '—',
                ], ';');
            }

            fclose($file);
        }, 200, $headers);
    }
}