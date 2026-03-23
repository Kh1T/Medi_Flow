<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('bed_type')->nullable()->after('payment_method');
            $table->integer('bed_days')->nullable()->after('bed_type');
            $table->decimal('bed_charges', 10, 2)->default(0)->after('bed_days');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['bed_type', 'bed_days', 'bed_charges']);
        });
    }
};
