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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Multilingual
            $table->json('short_description')->nullable(); // Multilingual
            $table->json('description')->nullable(); // Multilingual
            $table->string('icon')->nullable(); // e.g. "fas fa-code"
            $table->string('slug')->unique();
            $table->decimal('price', 20, 3)->nullable();
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->boolean('active')->default(true)->index();
            $table->unsignedBigInteger('thumbnail_id')->nullable(); // FK to media
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('thumbnail_id')->references('id')->on('media')->nullOnDelete();
        });

        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();

            $table->json('note')->nullable(); // Additional notes from customer

            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete(); // Optional
            $table->enum('payment_type', ['online', 'cod'])->default('cod'); // cod = Cash on Delivery

            $table->foreignId('currency_id')->constrained()->onDelete('cascade'); // Order-specific currency
            $table->decimal('price', 10, 2);

            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'refunded'])->default('unpaid');

            $table->json('form_data')->nullable(); // Custom form fields

            $table->dateTime('date')->nullable(); // Scheduled date for service

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['currency_id']);
        });

        Schema::dropIfExists('service_orders');
        Schema::dropIfExists('services');
    }
};