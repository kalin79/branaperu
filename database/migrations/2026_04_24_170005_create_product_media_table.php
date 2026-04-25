<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();

            $table->enum('media_type', ['image', 'video_mp4', 'youtube', 'vimeo'])
                  ->default('image');

            $table->string('file_url');           // URL del archivo (imagen/video)
            $table->string('thumbnail_url')->nullable();

            $table->boolean('is_main')->default(false);   // Imagen principal
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};