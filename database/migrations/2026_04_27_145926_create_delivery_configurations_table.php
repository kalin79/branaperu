<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_configurations', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_delivery_cost', 10, 2)->default(10.00);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // envío gratis a partir de X soles
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_configurations');
    }
};