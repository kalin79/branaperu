<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            // Snapshot adicional del producto
            if (!Schema::hasColumn('order_items', 'product_slug')) {
                $table->string('product_slug')->nullable();
            }

            if (!Schema::hasColumn('order_items', 'product_image')) {
                $table->string('product_image')->nullable();
            }

            if (!Schema::hasColumn('order_items', 'original_price')) {
                $table->decimal('original_price', 12, 2)->nullable();
            }

            // Notas específicas por línea de producto
            if (!Schema::hasColumn('order_items', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_slug',
                'product_image',
                'original_price',
                'notes',
            ]);
        });
    }
};