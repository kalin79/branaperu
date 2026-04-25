<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_features', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('image')->nullable();           // imagen/icono de la característica
            $table->text('description')->nullable();
            $table->string('icon')->nullable();            // icono (heroicon, fontawesome, etc.)
            
            $table->boolean('is_active')->default(true);
            
            $table->integer('order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_features');
    }
};