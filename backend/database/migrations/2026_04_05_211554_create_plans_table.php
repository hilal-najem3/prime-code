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
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');        // Basic
            $table->string('slug')->unique(); // basic

            $table->decimal('price', 10, 2)->default(0);

            $table->text('description')->nullable();

            $table->boolean('active')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('plan_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('plan_id');
            $table->uuid('module_id');

            $table->timestamps();

            $table->unique(['plan_id', 'module_id']);

            // Foreign keys
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_modules', function (Blueprint $table) {
            $table->dropForeign('plan_modules_plan_id_foreign');
            $table->dropForeign('plan_modules_module_id_foreign');
        });

        Schema::dropIfExists('plan_modules');

        Schema::dropIfExists('plans');
    }
};
