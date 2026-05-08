<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            $table->string('provider');                    // mercadopago, etc.
            $table->string('external_id')->nullable()->index(); // ID de MercadoPago

            $table->string('status');                      // pending, approved, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PEN');
            $table->string('payment_method')->nullable();  // credit_card, yape, etc.

            $table->json('payment_response')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Índices útiles para reportes
            $table->index(['status', 'provider']);
            $table->index('paid_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};