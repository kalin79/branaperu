<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_feature', function (Blueprint $table) {   // ← Nombre correcto según tu SQL original
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            $table->foreignId('feature_id')
                  ->constrained('product_features')
                  ->onDelete('cascade');

            $table->integer('orden')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['product_id', 'feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_feature');
    }
};