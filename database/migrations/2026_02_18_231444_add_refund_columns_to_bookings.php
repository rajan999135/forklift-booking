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
    Schema::table('bookings', function (Blueprint $table) {
        if (!Schema::hasColumn('bookings', 'refund_status')) {
            $table->string('refund_status')->nullable()->after('payment_status');
        }
        if (!Schema::hasColumn('bookings', 'refund_amount')) {
            $table->integer('refund_amount')->nullable()->after('refund_status');
        }
        if (!Schema::hasColumn('bookings', 'refunded_at')) {
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
        }
        if (!Schema::hasColumn('bookings', 'completed_at')) {
            $table->timestamp('completed_at')->nullable()->after('refunded_at');
        }
    });
}

public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn(['refund_status', 'refund_amount', 'refunded_at', 'completed_at']);
    });
}
};
