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
        Schema::create('patient_identities', function (Blueprint $table) {
            $table->id();

            // 🔗 Relation to patient
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();

            // 🪪 Identity info
            $table->enum('type', ['id_card', 'passport', 'driver_license']);
            $table->string('number')->nullable();

            // 📅 Dates
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();

            // 📝 Notes
            $table->text('notes')->nullable();

            $table->softDeletes();   // ✅ rule
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first (if needed)
        Schema::table('patient_identities', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });
        Schema::dropIfExists('patient_identities');
    }
};