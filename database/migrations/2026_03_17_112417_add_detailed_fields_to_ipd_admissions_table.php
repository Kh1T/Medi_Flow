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
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->enum('admission_type', ['Emergency', 'Planned'])->default('Planned')->after('bed_number');
            $table->text('symptoms')->nullable()->after('admission_reason');
            $table->text('diagnosis')->nullable()->after('symptoms');
            $table->text('discharge_summary')->nullable()->after('discharge_date');
            $table->decimal('total_bill', 10, 2)->nullable()->after('discharge_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->dropColumn(['admission_type', 'symptoms', 'diagnosis', 'discharge_summary', 'total_bill']);
        });
    }
};
