<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', [
                'scheduled', 'confirmed', 'checked_in',
                'completed', 'cancelled', 'no_show'
            ])->default('scheduled');
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date', 'appointment_time'], 'appt_conflict_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
