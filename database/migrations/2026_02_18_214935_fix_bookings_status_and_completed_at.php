<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand ENUM to include every value currently in the DB + new ones
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending',
            'awaiting',
            'awaiting_admin',
            'awaiting_payment',
            'confirmed',
            'approved',
            'rejected',
            'cancelled',
            'completed'
        ) NOT NULL DEFAULT 'pending'");

        // Step 2: Normalise old/inconsistent values
        DB::statement("UPDATE bookings SET status = 'confirmed'     WHERE status = 'approved'");
        DB::statement("UPDATE bookings SET status = 'awaiting_admin' WHERE status = 'awaiting'");
        DB::statement("UPDATE bookings SET status = 'awaiting_admin' WHERE status = 'awaiting_payment'");

        // Step 3: Shrink ENUM to final clean set
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending',
            'awaiting_admin',
            'confirmed',
            'rejected',
            'cancelled',
            'completed'
        ) NOT NULL DEFAULT 'pending'");

        // Step 4: Add completed_at if missing
        if (!Schema::hasColumn('bookings', 'completed_at')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->timestamp('completed_at')->nullable()->after('updated_at');
            });
        }

        // Step 5: Add refund columns if missing
        if (!Schema::hasColumn('bookings', 'refund_status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('refund_status')->nullable()->after('completed_at');
                $table->integer('refund_amount')->nullable()->after('refund_status');
                $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            });
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending','awaiting_admin','confirmed','cancelled'
        ) NOT NULL DEFAULT 'pending'");

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('bookings', 'refund_status')) {
                $table->dropColumn(['refund_status', 'refund_amount', 'refunded_at']);
            }
        });
    }
};