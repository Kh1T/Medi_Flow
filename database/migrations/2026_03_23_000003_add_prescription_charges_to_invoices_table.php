<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(0)->after('charges');
            $table->decimal('lab_charges', 10, 2)->default(0)->after('consultation_fee');
            $table->decimal('medicine_charges', 10, 2)->default(0)->after('lab_charges');
            $table->decimal('procedure_charges', 10, 2)->default(0)->after('medicine_charges');
            $table->decimal('prescription_charges', 10, 2)->default(0)->after('procedure_charges');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['consultation_fee', 'lab_charges', 'medicine_charges', 'procedure_charges', 'prescription_charges']);
        });
    }
};
