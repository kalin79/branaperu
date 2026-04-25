<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_sections', function (Blueprint $table) {
            $table->string('media_type')->nullable()->change();
            $table->string('file_media')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_sections', function (Blueprint $table) {
            $table->string('media_type')->nullable(false)->change();
            $table->string('file_media')->nullable(false)->change();
        });
    }
};