<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Make medical_record_id nullable to allow prescriptions directly from OPD/IPD visits
            $table->foreignId('medical_record_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Revert to required foreign key
            $table->foreignId('medical_record_id')->nullable(false)->change();
        });
    }
};
