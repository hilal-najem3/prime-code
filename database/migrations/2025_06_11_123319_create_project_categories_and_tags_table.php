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
        // Project Categories
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreignId('thumbnail_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('project_categories')->onDelete('cascade');
        });

        Schema::create('project_project_category', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('category_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('project_categories')->onDelete('cascade');
        });

        // Project Tags
        Schema::create('project_tags', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->timestamps();
        });

        Schema::create('project_project_tag', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('tag_id')->references('id')->on('project_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_project_tag', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['tag_id']);
        });
        Schema::dropIfExists('project_project_tag');

        Schema::dropIfExists('project_tags');

        Schema::table('project_project_category', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('project_project_category');

        Schema::table('project_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['thumbnail_id']);
        });
        Schema::dropIfExists('project_categories');
    }
};
