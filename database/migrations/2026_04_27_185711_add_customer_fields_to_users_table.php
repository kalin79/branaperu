<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Solo agregamos la columna si no existe
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'document_type')) {
                $table->enum('document_type', ['DNI', 'CE', 'Pasaporte'])->nullable();
            }

            if (!Schema::hasColumn('users', 'document_number')) {
                $table->string('document_number')->nullable()->unique();
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }

            if (!Schema::hasColumn('users', 'province')) {
                $table->string('province')->nullable();
            }

            if (!Schema::hasColumn('users', 'district_id')) {
                $table->foreignId('district_id')->nullable()->constrained('districts');
            }

            // Status (por si no existe)
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['activo', 'bloqueado'])->default('activo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'document_type',
                'document_number',
                'department',
                'province',
                'district_id'
            ]);
        });
    }
};