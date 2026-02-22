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
        // product Categories
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('thumbnail_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('product_categories')->onDelete('cascade');
        });

        Schema::create('product_product_category', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('category_id');

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
        });

        // product Tags
        Schema::create('product_tags', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->timestamps();
        });

        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('tag_id')->references('id')->on('product_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_product_tag', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['tag_id']);
        });
        Schema::dropIfExists('product_product_tag');

        Schema::dropIfExists('product_tags');

        Schema::table('product_product_category', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('product_product_category');

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['thumbnail_id']);
        });
        Schema::dropIfExists('product_categories');
    }
};