<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop legacy columns if they exist
            if (Schema::hasColumn('bookings', 'start_at')) {
                $table->dropColumn('start_at');
            }
            if (Schema::hasColumn('bookings', 'end_at')) {
                $table->dropColumn('end_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Recreate them as nullable if you ever roll back
            if (!Schema::hasColumn('bookings', 'start_at')) {
                $table->dateTime('start_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'end_at')) {
                $table->dateTime('end_at')->nullable();
            }
        });
    }
};
