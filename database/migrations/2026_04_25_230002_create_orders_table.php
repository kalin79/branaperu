<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('order_number')->unique();

            $table->decimal('subtotal', 12, 2);           // Total antes de descuentos
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_total', 12, 2);

            // Descuento automático aplicado
            $table->decimal('auto_discount_percent', 5, 2)->nullable();
            $table->decimal('auto_discount_min_amount', 12, 2)->nullable();

            // Cupón aplicado
            $table->foreignId('coupon_id')->nullable()->constrained('coupons');
            $table->string('coupon_code')->nullable();

            $table->string('status')->default('pending'); // pending, paid, processing, shipped, cancelled
            $table->string('payment_id')->nullable();     // ID de Mercado Pago
            $table->json('payment_response')->nullable();

            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};