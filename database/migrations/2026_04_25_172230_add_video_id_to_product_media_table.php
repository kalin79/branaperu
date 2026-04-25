<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_media', function (Blueprint $table) {
            $table->string('video_id')->nullable()->after('file_url');

            // Opcional: si quieres renombrar o hacer más claro
            // $table->renameColumn('file_url', 'file_url'); // ya existe
        });
    }

    public function down(): void
    {
        Schema::table('product_media', function (Blueprint $table) {
            $table->dropColumn('video_id');
        });
    }
};