<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Create Articles Table
|--------------------------------------------------------------------------
|
| Stores blog articles for each tenant.
| Supports multilingual content using JSON fields.
| Designed to be extension-ready (categories, tags, etc.).
|
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Content Fields (Multilingual)
            |--------------------------------------------------------------------------
            */

            $table->json('title');
            $table->json('slug');
            $table->json('content');

            $table->json('excerpt')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Publishing
            |--------------------------------------------------------------------------
            */

            $table->string('status')->default('draft'); // draft, published
            $table->timestamp('published_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Author
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('author_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SEO (Phase 1 basic)
            |--------------------------------------------------------------------------
            */

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | System
            |--------------------------------------------------------------------------
            */

            $table->softDeletes();
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('status');
            $table->index('published_at');
            $table->index('author_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};