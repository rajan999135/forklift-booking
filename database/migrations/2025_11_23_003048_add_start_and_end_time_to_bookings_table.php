<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Only add the columns if they don't already exist
            if (!Schema::hasColumn('bookings', 'start_time')) {
                $table->dateTime('start_time')->nullable()->after('forklift_id');
            }

            if (!Schema::hasColumn('bookings', 'end_time')) {
                $table->dateTime('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('bookings', 'end_time')) {
                $table->dropColumn('end_time');
            }
        });
    }
};
