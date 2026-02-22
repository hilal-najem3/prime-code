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
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('thumbnail_id')
                ->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('post_categories')->onDelete('cascade');
        });

        Schema::create('post_post_category', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('category_id');

            $table->foreign('category_id')->references('id')->on('post_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_post_category', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('post_post_category');

        Schema::table('post_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['thumbnail_id']);
        });
        Schema::dropIfExists('post_categories');
    }
};