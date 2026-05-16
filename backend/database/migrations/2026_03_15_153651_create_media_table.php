<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // Storage disk (public, s3, firebase, etc.)
            $table->string('disk')->default('public');

            // File path relative to disk
            $table->string('path');

            // Original filename
            $table->string('filename');

            // File extension
            $table->string('extension', 10);

            // Mime type
            $table->string('mime_type');

            // File size in bytes
            $table->unsignedBigInteger('size');

            // Optional alt text for images (SEO)
            $table->string('alt_text')->nullable();

            // Media grouping (avatar, gallery, featured, etc.)
            $table->string('collection')->nullable();

            // Polymorphic relationship
            $table->nullableUuidMorphs('model');

            // Uploader
            $table->uuid('user_id')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['collection']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
