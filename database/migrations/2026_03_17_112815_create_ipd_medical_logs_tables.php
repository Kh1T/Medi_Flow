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
        Schema::create('ipd_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipd_admission_id')->constrained('ipd_admissions')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->enum('note_type', ['nurse_chart', 'doctor_visit']);
            $table->text('notes');
            $table->timestamps();
        });

        Schema::create('ipd_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipd_admission_id')->constrained('ipd_admissions')->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage');
            $table->dateTime('administered_at');
            $table->foreignId('administered_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('ipd_lab_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipd_admission_id')->constrained('ipd_admissions')->cascadeOnDelete();
            $table->string('test_name');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('requested_at');
            $table->text('result_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_lab_requests');
        Schema::dropIfExists('ipd_medications');
        Schema::dropIfExists('ipd_notes');
    }
};
