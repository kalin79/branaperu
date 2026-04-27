<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Snapshot del Cupón
            if (!Schema::hasColumn('orders', 'coupon_name')) {
                $table->string('coupon_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'coupon_discount_value')) {
                $table->decimal('coupon_discount_value', 12, 2)->default(0);
            }

            // Snapshot de la Regla de Descuento Automático
            if (!Schema::hasColumn('orders', 'discount_rule_name')) {
                $table->string('discount_rule_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'discount_rule_min_amount')) {
                $table->decimal('discount_rule_min_amount', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('orders', 'discount_rule_percent')) {
                $table->decimal('discount_rule_percent', 5, 2)->nullable();
            }

            // Consentimientos del usuario (obligatorios y opcional)
            if (!Schema::hasColumn('orders', 'accepted_terms')) {
                $table->boolean('accepted_terms')->default(false);
            }
            if (!Schema::hasColumn('orders', 'accepted_privacy')) {
                $table->boolean('accepted_privacy')->default(false);
            }
            if (!Schema::hasColumn('orders', 'accepted_marketing')) {
                $table->boolean('accepted_marketing')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_name',
                'coupon_discount_value',
                'discount_rule_name',
                'discount_rule_min_amount',
                'discount_rule_percent',
                'accepted_terms',
                'accepted_privacy',
                'accepted_marketing',
            ]);
        });
    }
};