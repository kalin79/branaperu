<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // === CAMPOS PARA INVITADOS ===
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();

            // === CAMPOS PARA DELIVERY ===
            $table->foreignId('delivery_district_id')->nullable()->constrained('districts');
            $table->decimal('delivery_cost', 12, 2)->default(0);
            $table->text('delivery_address')->nullable();
            $table->text('delivery_reference')->nullable();
            $table->string('delivery_full_name')->nullable();

            // Hacemos user_id nullable (para permitir compras como invitado)
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_email',
                'guest_phone',
                'delivery_district_id',
                'delivery_cost',
                'delivery_address',
                'delivery_reference',
                'delivery_full_name',
            ]);

            // Volvemos user_id a required
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};