<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // === Cliente ===
            $table->string('guest_last_name')->nullable()->after('guest_name');
            $table->string('dni', 20)->nullable()->after('guest_phone');

            // === Método de entrega: delivery | pickup ===
            $table->string('delivery_method', 20)
                ->default('delivery')
                ->after('delivery_district_id');

            // === Snapshot del local (cuando es pickup) ===
            $table->foreignId('pickup_local_id')
                ->nullable()
                ->after('delivery_method')
                ->constrained('locals')
                ->nullOnDelete();
            $table->string('pickup_local_name')->nullable()->after('pickup_local_id');
            $table->string('pickup_local_address')->nullable()->after('pickup_local_name');

            // === Documento de facturación: boleta | factura ===
            $table->string('document_type', 20)
                ->default('boleta')
                ->after('pickup_local_address');
            $table->string('billing_ruc', 20)->nullable()->after('document_type');
            $table->string('billing_business_name')->nullable()->after('billing_ruc');
            $table->string('billing_address')->nullable()->after('billing_business_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pickup_local_id']);
            $table->dropColumn([
                'guest_last_name',
                'dni',
                'delivery_method',
                'pickup_local_id',
                'pickup_local_name',
                'pickup_local_address',
                'document_type',
                'billing_ruc',
                'billing_business_name',
                'billing_address',
            ]);
        });
    }
};