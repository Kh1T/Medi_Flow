<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('charges', 10, 2)->default(0)->after('invoice_number');
            $table->decimal('contractual_adjustments', 10, 2)->default(0)->after('charges');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['charges', 'contractual_adjustments']);
        });
    }
};
