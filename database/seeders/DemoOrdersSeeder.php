<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\DiscountRule;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\District;
use App\Models\Local;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * DemoOrdersSeeder
 * -----------------------------------------------------------------------------
 * Genera órdenes de prueba con:
 *   - Pagos en todos los estados de MercadoPago
 *     (approved / pending / rejected / in_process / refunded / chargeback)
 *   - Fechas distribuidas en los últimos 90 días
 *   - Aplicación de cupones (Lanzamiento Web, Verano2026) y de la
 *     regla de descuento automático (Para Emprededores) respetando sus
 *     restricciones de monto mínimo, fechas de vigencia y máximo de usos.
 *
 * No depende de Faker. Usa helpers nativos de PHP + listas hardcodeadas
 * para nombres / direcciones / empresas. Funciona aunque Faker no esté
 * instalado.
 *
 * Uso:
 *   php artisan db:seed --class=DemoOrdersSeeder
 *   php artisan db:seed --class=DemoOrdersSeeder -- --fresh
 */
class DemoOrdersSeeder extends Seeder
{
    /** Cantidad total de órdenes a generar */
    private const TOTAL_ORDERS = 120;

    /** Rango: últimos N días (incluye hoy) */
    private const DAYS_BACK = 90;

    /** Marca para reconocer las órdenes demo (útil para limpiar) */
    private const DEMO_TAG = '[DEMO_SEED]';

    /**
     * Distribución de estados de pago (suma = 100)
     */
    private array $statusWeights = [
        Payment::STATUS_APPROVED => 65,
        Payment::STATUS_PENDING => 10,
        Payment::STATUS_IN_PROCESS => 7,
        Payment::STATUS_REJECTED => 10,
        Payment::STATUS_REFUNDED => 5,
        Payment::STATUS_CHARGEBACK => 3,
    ];

    /**
     * Distribución de estrategia de descuento (suma = 100)
     */
    private array $discountWeights = [
        'none' => 55,
        'coupon_fixed' => 15,
        'coupon_pct' => 20,
        'auto_rule' => 10,
    ];

    // ---------------- Listas para data ficticia ----------------
    private array $firstNames = [
        'Juan',
        'Carlos',
        'María',
        'Lucía',
        'Pedro',
        'Sofía',
        'Diego',
        'Andrea',
        'José',
        'Camila',
        'Luis',
        'Valeria',
        'Miguel',
        'Daniela',
        'Jorge',
        'Patricia',
        'Fernando',
        'Gabriela',
        'Ricardo',
        'Paula',
        'Sebastián',
        'Ximena',
        'Andrés',
        'Isabella',
        'Manuel',
    ];

    private array $lastNames = [
        'Pérez',
        'Rodríguez',
        'Quispe',
        'Mendoza',
        'Castro',
        'Flores',
        'Vargas',
        'Ramírez',
        'Torres',
        'Gómez',
        'Ríos',
        'Vega',
        'Salazar',
        'Cabrera',
        'Huamán',
        'Espinoza',
        'Reyes',
        'Cárdenas',
        'Bravo',
        'Morales',
        'Cruz',
        'Silva',
        'Núñez',
        'Mejía',
        'Paredes',
    ];

    private array $streets = [
        'Av. Javier Prado',
        'Av. Arequipa',
        'Av. La Marina',
        'Jr. de la Unión',
        'Av. Brasil',
        'Av. Salaverry',
        'Av. Petit Thouars',
        'Calle Las Begonias',
        'Av. Benavides',
        'Av. Larco',
        'Av. Pardo',
        'Calle Schell',
        'Av. Angamos',
        'Av. Tomás Marsano',
        'Av. Aviación',
    ];

    private array $districtsList = ['Miraflores', 'San Isidro', 'Surco', 'San Borja', 'Lince', 'Magdalena', 'Jesús María', 'Barranco'];

    private array $companies = [
        'Inversiones Andinas SAC',
        'Distribuidora Lima SRL',
        'Comercial Pacífico EIRL',
        'Grupo Atlas SAC',
        'Servicios Integrales del Sur SAC',
        'Importadora Norte SAC',
        'Negocios Globales SRL',
        'Andean Trading SAC',
        'Solutions Perú SAC',
        'Grupo Industrial Costa SAC',
    ];

    private array $emailDomains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'];

    private array $paymentMethods = ['visa', 'master', 'amex', 'yape', 'plin', 'pagoefectivo'];

    public function run(): void
    {
        if (in_array('--fresh', $_SERVER['argv'] ?? [], true)) {
            $this->clean();
        }

        // 1. Asegura cupones y regla
        $couponLanzamiento = $this->ensureLanzamientoCoupon();
        $couponVerano = $this->ensureVeranoCoupon();
        $autoRule = $this->ensureEmprededoresRule();

        // Contadores para respetar el max_uses
        $usedCouponLanzamiento = (int) DB::table('orders')
            ->where('coupon_id', $couponLanzamiento->id)
            ->count();
        $usedCouponVerano = (int) DB::table('orders')
            ->where('coupon_id', $couponVerano->id)
            ->count();

        // 2. Catálogos
        $products = Product::query()->where('is_active', true)->get();
        $districts = District::query()->get();
        $locals = Local::query()->get();

        if ($products->isEmpty()) {
            $this->command?->warn('No hay productos activos. Crea al menos 1 producto antes de correr este seeder.');
            return;
        }

        $created = 0;

        DB::transaction(function () use ($products, $districts, $locals, $couponLanzamiento, $couponVerano, $autoRule, &$usedCouponLanzamiento, &$usedCouponVerano, &$created) {
            for ($i = 0; $i < self::TOTAL_ORDERS; $i++) {
                $status = $this->pickWeightedStatus();
                $createdAt = $this->randomDateInRange();
                $deliveryMethod = $this->boolean(70)
                    ? Order::DELIVERY_METHOD_DELIVERY
                    : Order::DELIVERY_METHOD_PICKUP;
                $docType = $this->boolean(75)
                    ? Order::DOCUMENT_TYPE_BOLETA
                    : Order::DOCUMENT_TYPE_FACTURA;

                $itemsCount = rand(1, min(5, $products->count()));
                $selected = $products->random($itemsCount);

                $subtotal = 0.0;
                $itemsPayload = [];

                foreach ($selected as $product) {
                    $qty = rand(1, 4);
                    $price = (float) $product->price;
                    $line = round($price * $qty, 2);
                    $subtotal += $line;

                    $itemsPayload[] = [
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                        'product_image' => $product->cover_image,
                        'ml' => $product->ml,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'original_price' => $product->old_price ?? $price,
                        'subtotal' => $line,
                    ];
                }

                // ---------------- DESCUENTO ----------------
                $strategy = $this->pickWeightedDiscount();

                $discountAmount = 0.0;
                $couponSnapshot = null;
                $autoRuleSnapshot = null;

                if ($strategy === 'coupon_fixed') {
                    if (
                        $this->dateWithin($createdAt, $couponLanzamiento->starts_at, $couponLanzamiento->expires_at)
                        && $subtotal >= (float) $couponLanzamiento->min_purchase_amount
                        && $usedCouponLanzamiento < (int) $couponLanzamiento->max_uses
                    ) {
                        $discountAmount = (float) $couponLanzamiento->calculateDiscount($subtotal);
                        $couponSnapshot = [
                            'id' => $couponLanzamiento->id,
                            'code' => $couponLanzamiento->code,
                            'name' => $couponLanzamiento->name,
                            'value' => (float) $couponLanzamiento->discount_value,
                        ];
                        $usedCouponLanzamiento++;
                    }
                } elseif ($strategy === 'coupon_pct') {
                    if ($this->dateWithin($createdAt, $couponVerano->starts_at, $couponVerano->expires_at)) {
                        $discountAmount = (float) $couponVerano->calculateDiscount($subtotal);
                        $couponSnapshot = [
                            'id' => $couponVerano->id,
                            'code' => $couponVerano->code,
                            'name' => $couponVerano->name,
                            'value' => (float) $couponVerano->discount_value,
                        ];
                        $usedCouponVerano++;
                    }
                } elseif ($strategy === 'auto_rule') {
                    if ($subtotal >= (float) $autoRule->min_amount && $autoRule->is_active) {
                        $discountAmount = (float) $autoRule->calculateDiscount($subtotal);
                        $autoRuleSnapshot = [
                            'name' => $autoRule->name,
                            'min_amount' => (float) $autoRule->min_amount,
                            'percent' => (float) $autoRule->discount_percent,
                        ];
                    }
                }

                $deliveryCost = $deliveryMethod === Order::DELIVERY_METHOD_DELIVERY
                    ? (float) $this->randomElement([8, 10, 12, 15, 18, 20])
                    : 0.0;

                $finalTotal = round($subtotal - $discountAmount + $deliveryCost, 2);
                $orderStatus = $this->mapOrderStatusFromPayment($status);

                $firstName = $this->randomElement($this->firstNames);
                $lastName = $this->randomElement($this->lastNames);

                // ---------------- ORDEN ----------------
                $orderData = [
                    'user_id' => null,
                    'order_number' => $this->generateOrderNumber($createdAt, $i),
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'final_total' => $finalTotal,

                    'status' => $orderStatus,

                    'guest_name' => $firstName,
                    'guest_last_name' => $lastName,
                    'guest_email' => $this->buildEmail($firstName, $lastName, $i),
                    'guest_phone' => '9' . $this->numerify(8),
                    'dni' => $this->numerify(8),

                    'delivery_method' => $deliveryMethod,
                    'document_type' => $docType,

                    'accepted_terms' => true,
                    'accepted_privacy' => true,
                    'accepted_marketing' => $this->boolean(40),

                    'notes' => self::DEMO_TAG . ($this->boolean(40) ? ' Pedido prueba demo' : ''),

                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                if ($couponSnapshot) {
                    $orderData['coupon_id'] = $couponSnapshot['id'];
                    $orderData['coupon_code'] = $couponSnapshot['code'];
                    $orderData['coupon_name'] = $couponSnapshot['name'];
                    $orderData['coupon_discount_value'] = $couponSnapshot['value'];
                }

                if ($autoRuleSnapshot) {
                    $orderData['discount_rule_name'] = $autoRuleSnapshot['name'];
                    $orderData['discount_rule_min_amount'] = $autoRuleSnapshot['min_amount'];
                    $orderData['discount_rule_percent'] = $autoRuleSnapshot['percent'];
                }

                if ($deliveryMethod === Order::DELIVERY_METHOD_DELIVERY) {
                    $district = $districts->isNotEmpty() ? $districts->random() : null;
                    $orderData['delivery_district_id'] = $district?->id;
                    $orderData['delivery_cost'] = $deliveryCost;
                    $orderData['shipping_address'] = $this->randomElement($this->streets) . ' ' . rand(100, 9999);
                    $orderData['delivery_reference'] = $this->boolean(50) ? 'Casa de color ' . $this->randomElement(['blanco', 'azul', 'rojo', 'beige']) : null;
                    $orderData['delivery_full_name'] = $firstName . ' ' . $lastName;
                } else {
                    $local = $locals->isNotEmpty() ? $locals->random() : null;
                    $orderData['pickup_local_id'] = $local?->id;
                    $orderData['pickup_local_name'] = $local?->name ?? 'Local Centro';
                    $orderData['pickup_local_address'] = $local?->address ?? 'Av. Principal 123';
                }

                if ($docType === Order::DOCUMENT_TYPE_FACTURA) {
                    $orderData['billing_ruc'] = '20' . $this->numerify(9);
                    $orderData['billing_business_name'] = $this->randomElement($this->companies);
                    $orderData['billing_address'] = $this->randomElement($this->streets) . ' ' . rand(100, 9999) . ', ' . $this->randomElement($this->districtsList);
                }

                $order = Order::create($orderData);

                // ---------------- ITEMS ----------------
                foreach ($itemsPayload as $payload) {
                    $payload['order_id'] = $order->id;
                    $payload['created_at'] = $createdAt;
                    $payload['updated_at'] = $createdAt;
                    OrderItem::create($payload);
                }

                // ---------------- PAGO ----------------
                $paidAt = null;
                $failedAt = null;

                if ($status === Payment::STATUS_APPROVED || $status === Payment::STATUS_REFUNDED) {
                    $paidAt = (clone $createdAt)->addMinutes(rand(5, 240));
                }
                if (in_array($status, [Payment::STATUS_REJECTED, Payment::STATUS_CHARGEBACK], true)) {
                    $failedAt = (clone $createdAt)->addMinutes(rand(2, 60));
                }

                $externalId = 'MP-' . strtoupper(Str::random(10));

                Payment::create([
                    'order_id' => $order->id,
                    'provider' => 'mercadopago',
                    'external_id' => $externalId,
                    'status' => $status,
                    'amount' => $finalTotal,
                    'currency' => 'PEN',
                    'payment_method' => $this->randomElement($this->paymentMethods),
                    'payment_response' => [
                        'id' => $externalId,
                        'status' => $status,
                        'status_detail' => $this->statusDetailFor($status),
                        'transaction_amount' => $finalTotal,
                        'demo' => true,
                    ],
                    'paid_at' => $paidAt,
                    'failed_at' => $failedAt,
                    'created_at' => $createdAt,
                    'updated_at' => $paidAt ?? $failedAt ?? $createdAt,
                ]);

                $created++;
            }
        });

        $this->command?->info("✓ Se crearon {$created} órdenes demo distribuidas en los últimos " . self::DAYS_BACK . " días.");
        $this->printSummary();
    }

    public function clean(): void
    {
        $orderIds = Order::query()
            ->where('notes', 'like', self::DEMO_TAG . '%')
            ->pluck('id');

        if ($orderIds->isEmpty()) {
            $this->command?->info('No había órdenes demo previas que limpiar.');
            return;
        }

        DB::transaction(function () use ($orderIds) {
            Payment::whereIn('order_id', $orderIds)->forceDelete();
            OrderItem::whereIn('order_id', $orderIds)->delete();
            Order::whereIn('id', $orderIds)->forceDelete();
        });

        $this->command?->warn("✗ Se eliminaron {$orderIds->count()} órdenes demo previas.");
    }

    // ============================================================
    // Cupones / Regla
    // ============================================================

    private function ensureLanzamientoCoupon(): Coupon
    {
        return Coupon::updateOrCreate(
            ['code' => 'Lanzamiento Web'],
            [
                'name' => 'Lanzamiento Web',
                'discount_type' => 'fixed',
                'discount_value' => 100,
                'min_purchase_amount' => 500,
                'max_uses' => 50,
                'max_uses_per_user' => 1,
                'starts_at' => Carbon::parse('2026-03-01 11:00:00'),
                'expires_at' => Carbon::parse('2026-03-28 11:00:00'),
                'is_active' => true,
            ]
        );
    }

    private function ensureVeranoCoupon(): Coupon
    {
        return Coupon::updateOrCreate(
            ['code' => 'Verano2026'],
            [
                'name' => 'Victor Influencer',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_purchase_amount' => 0,
                'max_uses' => null,
                'max_uses_per_user' => 1,
                'starts_at' => Carbon::parse('2026-03-13 23:00:00'),
                'expires_at' => Carbon::parse('2026-05-21 22:00:00'),
                'is_active' => true,
            ]
        );
    }

    private function ensureEmprededoresRule(): DiscountRule
    {
        return DiscountRule::updateOrCreate(
            ['name' => 'Para Emprededores'],
            [
                'min_amount' => 500,
                'discount_percent' => 20,
                'is_active' => true,
            ]
        );
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function pickWeightedStatus(): string
    {
        return $this->weightedPick($this->statusWeights);
    }

    private function pickWeightedDiscount(): string
    {
        return $this->weightedPick($this->discountWeights);
    }

    private function weightedPick(array $weights): string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $acc = 0;

        foreach ($weights as $key => $weight) {
            $acc += $weight;
            if ($rand <= $acc) {
                return (string) $key;
            }
        }

        return (string) array_key_first($weights);
    }

    private function dateWithin(Carbon $date, ?Carbon $start, ?Carbon $end): bool
    {
        if ($start && $date->lt($start)) {
            return false;
        }
        if ($end && $date->gt($end)) {
            return false;
        }
        return true;
    }

    private function mapOrderStatusFromPayment(string $paymentStatus): string
    {
        return match ($paymentStatus) {
            Payment::STATUS_APPROVED => $this->randomElement([
                Order::STATUS_PREPARING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
            ]),
            Payment::STATUS_PENDING, Payment::STATUS_IN_PROCESS => Order::STATUS_PENDING,
            Payment::STATUS_REJECTED => $this->randomElement([Order::STATUS_PENDING, Order::STATUS_CANCELLED]),
            Payment::STATUS_REFUNDED => Order::STATUS_REFUNDED,
            Payment::STATUS_CHARGEBACK => Order::STATUS_RETURNED,
            default => Order::STATUS_PENDING,
        };
    }

    private function randomDateInRange(): Carbon
    {
        $daysAgo = rand(0, self::DAYS_BACK - 1);
        return now()
            ->subDays($daysAgo)
            ->setTime(rand(8, 22), rand(0, 59), rand(0, 59));
    }

    private function generateOrderNumber(Carbon $date, int $index): string
    {
        return 'ORD-' . $date->format('Ymd') . '-' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT);
    }

    private function statusDetailFor(string $status): string
    {
        return match ($status) {
            Payment::STATUS_APPROVED => 'accredited',
            Payment::STATUS_PENDING => 'pending_waiting_payment',
            Payment::STATUS_IN_PROCESS => 'pending_review_manual',
            Payment::STATUS_REJECTED => 'cc_rejected_insufficient_amount',
            Payment::STATUS_REFUNDED => 'refunded',
            Payment::STATUS_CHARGEBACK => 'chargeback',
            default => 'unknown',
        };
    }

    /**
     * Helpers nativos (reemplazan a Faker)
     */
    private function randomElement(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    private function boolean(int $percentTrue = 50): bool
    {
        return rand(1, 100) <= $percentTrue;
    }

    private function numerify(int $length): string
    {
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= rand(0, 9);
        }
        return $out;
    }

    private function buildEmail(string $first, string $last, int $i): string
    {
        $slug = Str::ascii(Str::lower($first . '.' . $last));
        return $slug . $i . '@' . $this->randomElement($this->emailDomains);
    }

    private function printSummary(): void
    {
        $byStatus = DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('orders.notes', 'like', self::DEMO_TAG . '%')
            ->select('payments.status', DB::raw('COUNT(*) as c'), DB::raw('SUM(payments.amount) as total'))
            ->groupBy('payments.status')
            ->get();

        $this->command?->line('');
        $this->command?->line('Resumen por estado de pago (solo demo):');
        foreach ($byStatus as $r) {
            $this->command?->line(sprintf(
                '  - %-12s %4d órdenes   S/ %s',
                $r->status,
                $r->c,
                number_format((float) $r->total, 2)
            ));
        }

        $withCouponFixed = Order::where('notes', 'like', self::DEMO_TAG . '%')
            ->where('coupon_code', 'Lanzamiento Web')->count();
        $withCouponPct = Order::where('notes', 'like', self::DEMO_TAG . '%')
            ->where('coupon_code', 'Verano2026')->count();
        $withAutoRule = Order::where('notes', 'like', self::DEMO_TAG . '%')
            ->whereNotNull('discount_rule_name')->count();
        $noDiscount = Order::where('notes', 'like', self::DEMO_TAG . '%')
            ->where('discount_amount', 0)->count();

        $this->command?->line('');
        $this->command?->line('Resumen por descuento aplicado:');
        $this->command?->line("  - Cupón Lanzamiento Web (S/100):   {$withCouponFixed}");
        $this->command?->line("  - Cupón Verano2026 (10%):          {$withCouponPct}");
        $this->command?->line("  - Regla Para Emprededores (20%):   {$withAutoRule}");
        $this->command?->line("  - Sin descuento:                   {$noDiscount}");
    }
}