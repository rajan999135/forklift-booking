<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Only add if it does NOT exist
            if (!Schema::hasColumn('bookings', 'invoice_number')) {
                $table->string('invoice_number', 40)
                      ->nullable()
                      ->after('amount_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Only drop if the column exists
            if (Schema::hasColumn('bookings', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
        });
    }
};
