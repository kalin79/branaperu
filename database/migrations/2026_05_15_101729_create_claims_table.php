<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();

            // Correlativo público (ej. LR-2026-00001)
            $table->string('claim_number', 30)->unique();

            // Tipo: reclamo o queja
            $table->enum('claim_type', ['reclamo', 'queja']);

            // Identificación del consumidor
            $table->string('consumer_first_name', 100);
            $table->string('consumer_last_name', 100);
            $table->enum('consumer_document_type', ['DNI', 'CE', 'PASAPORTE', 'RUC']);
            $table->string('consumer_document_number', 20);
            $table->string('consumer_phone', 20);
            $table->string('consumer_email', 150);

            // Identificación del bien
            $table->string('product_name', 200)->nullable();
            $table->string('order_number', 50)->nullable();
            $table->text('product_description')->nullable();

            // Detalle del reclamo
            $table->text('claim_detail');
            $table->text('consumer_request');

            // Estado y respuesta
            $table->enum('status', ['pendiente', 'en_revision', 'atendido', 'rechazado'])
                ->default('pendiente');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();

            // Aceptación de términos
            $table->boolean('accepted_terms')->default(false);

            // Metadatos
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('claim_type');
            $table->index('consumer_document_number');
            $table->index('consumer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};