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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // 🔗 User relation (optional login)
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('allow_login')->default(false);

            // 🧑 Identity
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();

            // 📞 Contact
            $table->string('phone')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();

            // 📍 Address (JSON)
            $table->json('address')->nullable();

            // 🏥 Medical basics
            $table->string('blood_type')->nullable();
            $table->text('allergies')->nullable();

            // ⚙️ System
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();

            $table->softDeletes();   // ✅ before timestamps (rule)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first (if needed)
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('patients');
    }
};
