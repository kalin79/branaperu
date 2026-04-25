<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();

            $table->decimal('discount_value', 12, 2);
            $table->enum('discount_type', ['fixed', 'percent']);

            $table->decimal('min_purchase_amount', 12, 2)->default(0);
            $table->integer('max_uses')->nullable();           // usos totales
            $table->integer('max_uses_per_user')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};