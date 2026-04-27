<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            // Snapshot de la imagen del producto
            if (!Schema::hasColumn('order_items', 'product_image')) {
                $table->string('product_image')->nullable();
            }

            // Precio original antes de descuento
            if (!Schema::hasColumn('order_items', 'original_price')) {
                $table->decimal('original_price', 12, 2)->nullable();
            }

            // Índice compuesto (solo lo crea si no existe)
            if (!Schema::hasIndex('order_items', 'order_items_order_id_product_id_index')) {
                $table->index(['order_id', 'product_id'], 'order_items_order_id_product_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_image',
                'original_price',
            ]);

            // Solo eliminamos el índice si existe
            if (Schema::hasIndex('order_items', 'order_items_order_id_product_id_index')) {
                $table->dropIndex('order_items_order_id_product_id_index');
            }
        });
    }
};