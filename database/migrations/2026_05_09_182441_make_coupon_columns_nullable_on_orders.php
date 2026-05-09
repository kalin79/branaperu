<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()->change();
            $table->string('coupon_code')->nullable()->change();
            $table->string('coupon_name')->nullable()->change();
            $table->decimal('coupon_discount_value', 10, 2)->nullable()->change();

            // Aprovecha y haz lo mismo con la regla automática por si acaso
            $table->string('discount_rule_name')->nullable()->change();
            $table->decimal('discount_rule_min_amount', 10, 2)->nullable()->change();
            $table->decimal('discount_rule_percent', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No revertimos para no perder datos
    }
};