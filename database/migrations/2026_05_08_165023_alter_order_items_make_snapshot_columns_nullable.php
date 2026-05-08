<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('sku')->nullable()->change();
            $table->string('product_slug')->nullable()->change();
            $table->string('product_image')->nullable()->change();
            $table->integer('ml')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
            $table->string('product_slug')->nullable(false)->change();
            $table->string('product_image')->nullable(false)->change();
            $table->integer('ml')->nullable(false)->change();
        });
    }
};