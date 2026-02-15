<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('provider_name');
            $table->string('policy_number');
            $table->decimal('coverage_percentage', 5, 2)->default(0);
            $table->date('valid_until');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
