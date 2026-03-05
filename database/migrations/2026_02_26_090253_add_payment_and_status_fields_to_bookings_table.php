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
            // Rename 'address' → 'service_address' only if it exists and target does not exist
            if (Schema::hasColumn('bookings', 'address') && !Schema::hasColumn('bookings', 'service_address')) {
                $table->renameColumn('address', 'service_address');
            }

            // Add new columns if they do not already exist
            if (!Schema::hasColumn('bookings', 'status')) {
                $table->string('status')->default('pending')->after('service_address');
            }

            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }

            if (!Schema::hasColumn('bookings', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('completed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Reverse the rename safely
            if (Schema::hasColumn('bookings', 'service_address') && !Schema::hasColumn('bookings', 'address')) {
                $table->renameColumn('service_address', 'address');
            }

            // Drop columns if they exist
            if (Schema::hasColumn('bookings', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('bookings', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('bookings', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};