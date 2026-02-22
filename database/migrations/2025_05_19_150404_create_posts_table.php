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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('link')->nullable()->unique();
            $table->json('title'); // Translatable
            $table->json('content'); // Translatable
            $table->foreignId('thumbnail_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('post_comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('posts');
    }
};