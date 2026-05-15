<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->renameColumn('image', 'image_pc');
            $table->string('image_mobile')->nullable()->after('image_pc');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->renameColumn('image_pc', 'image');
            $table->dropColumn('image_mobile');
        });
    }
};
