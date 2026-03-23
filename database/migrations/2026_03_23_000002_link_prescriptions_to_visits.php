<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('opd_visit_id')->nullable()->after('doctor_id')->constrained('opd_visits')->nullOnDelete();
            $table->foreignId('ipd_admission_id')->nullable()->after('opd_visit_id')->constrained('ipd_admissions')->nullOnDelete();
            $table->string('visit_type')->nullable()->after('ipd_admission_id'); // 'opd' or 'ipd'
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['opd_visit_id']);
            $table->dropForeign(['ipd_admission_id']);
            $table->dropColumn(['opd_visit_id', 'ipd_admission_id', 'visit_type']);
        });
    }
};
