<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('cascade');

            $table->string('name', 200);
            $table->string('subtitle', 200)->nullable();
            $table->string('sku')->unique()->nullable();
            
            $table->decimal('price', 12, 2);
            $table->decimal('old_price', 12, 2)->nullable();

            $table->string('short_description', 255)->nullable();
            $table->longText('description')->nullable();

            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false);     // producto destacado

            $table->integer('stock')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};