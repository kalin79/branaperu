<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            $table->string('section_type');                    // Ej: 'description', 'features', 'specifications', 'video', etc.
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->longText('content')->nullable();           // Para texto rico / HTML

            $table->enum('media_type', ['image', 'video_mp4', 'youtube', 'vimeo', 'none'])
                  ->default('none');

            $table->string('file_media')->nullable();          // URL del archivo o embed
            $table->string('settings')->nullable();            // JSON para configuración extra

            $table->integer('orden')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sections');
    }
};