<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Create Pages Table
|--------------------------------------------------------------------------
|
| Stores dynamic pages for tenant websites.
| Supports multilingual content, SEO, and flexible layouts.
|
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {

            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Core Content
            |--------------------------------------------------------------------------
            */

            $table->json('title');
            $table->json('slug');

            $table->string('layout')->nullable(); // future theme binding
            $table->json('content')->nullable();  // flexible content structure

            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_homepage')->default(false);

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | System
            |--------------------------------------------------------------------------
            */

            $table->softDeletes(); // MUST be before timestamps (rule)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
