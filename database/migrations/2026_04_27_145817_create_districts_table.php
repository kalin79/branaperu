<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('ubigeo', 6)->unique();           // Código INEI (ej: 150101)
            $table->string('department');                    // Departamento (Lima, Arequipa, etc.)
            $table->string('province');                      // Provincia
            $table->string('district');                      // Distrito
            $table->decimal('delivery_cost', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index(['department', 'province', 'district']);
            $table->index('ubigeo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};