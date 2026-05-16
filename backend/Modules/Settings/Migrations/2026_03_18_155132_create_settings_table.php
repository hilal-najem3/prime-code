<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Create Settings Table (Tenant Database)
|--------------------------------------------------------------------------
|
| This table stores dynamic configuration values per tenant.
| It supports flexible JSON values, grouping, and public exposure.
|
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // Unique key for the setting (e.g. site.name, seo.meta_title)
            $table->string('key')->index();

            // JSON value for flexibility (supports multilingual, objects, etc.)
            $table->json('value')->nullable();

            // Data type (string, boolean, number, json)
            $table->string('type')->default('string');

            // Grouping for UI organization (general, seo, email, etc.)
            $table->string('group')->nullable();

            // Determines if setting is accessible publicly (frontend)
            $table->boolean('is_public')->default(false);

            // Optional: soft deletes for safety
            $table->softDeletes(); // MUST be before timestamps (rule)

            $table->timestamps();

            // Ensure unique key per tenant DB
            $table->unique(['key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
