<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add columns only if they don't already exist
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('status');
            }
            if (!Schema::hasColumn('bookings', 'refund_status')) {
                $table->string('refund_status')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->unsignedBigInteger('refund_amount')->nullable()->after('refund_status');
            }
            if (!Schema::hasColumn('bookings', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('bookings', 'stripe_refund_id')) {
                $table->string('stripe_refund_id')->nullable()->after('refunded_at');
            }
            if (!Schema::hasColumn('bookings', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('stripe_refund_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'refund_status',
                'refund_amount',
                'refunded_at',
                'stripe_refund_id',
                'completed_at',
            ]);
        });
    }
};