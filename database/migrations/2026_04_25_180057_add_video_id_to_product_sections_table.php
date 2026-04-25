<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_sections', function (Blueprint $table) {
            $table->string('video_id')->nullable()->after('file_media');
        });
    }

    public function down(): void
    {
        Schema::table('product_sections', function (Blueprint $table) {
            $table->dropColumn('video_id');
        });
    }
};