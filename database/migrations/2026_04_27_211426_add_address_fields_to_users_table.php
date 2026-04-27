<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Dirección completa
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('district_id');
            }

            // Referencia de la dirección
            if (!Schema::hasColumn('users', 'address_reference')) {
                $table->text('address_reference')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'address_reference']);
        });
    }
};