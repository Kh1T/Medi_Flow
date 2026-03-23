<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->dropColumn(['ward_type', 'bed_number', 'total_bill']);
        });
    }

    public function down(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->string('ward_type')->nullable()->after('discharge_date');
            $table->string('bed_number')->nullable()->after('ward_type');
            $table->decimal('total_bill', 10, 2)->nullable()->after('discharge_summary');
        });
    }
};
