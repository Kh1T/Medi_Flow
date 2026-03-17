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
        Schema::table('opd_visits', function (Blueprint $table) {
            $table->enum('visit_type', ['New', 'Follow-up'])->default('New')->after('diagnosis');
            $table->enum('payment_status', ['Paid', 'Unpaid', 'Pending'])->default('Unpaid')->after('fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd_visits', function (Blueprint $table) {
            $table->dropColumn(['visit_type', 'payment_status']);
        });
    }
};
