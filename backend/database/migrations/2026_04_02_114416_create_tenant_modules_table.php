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
        Schema::create('tenant_modules', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('module_id');

            $table->timestamps();

            $table->unique(['tenant_id', 'module_id']);

            // Foreign keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_modules', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['module_id']);
            $table->dropUnique(['tenant_id', 'module_id']);
            $table->dropColumn(['tenant_id', 'module_id']);
        });

        // Drop the tenant_modules table if it exists
        Schema::dropIfExists('tenant_modules');
    }
};