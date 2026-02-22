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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('type'); // e.g., text, select, multiselect
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->json('value');
            $table->string('color')->nullable(); // Optional, for color attributes
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['attribute_value_id']);
        });
        Schema::dropIfExists('product_attribute_values');
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropForeign(['attribute_id']);
        });
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
