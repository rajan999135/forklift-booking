<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'hourly_rate_cents')) {
                $table->dropColumn('hourly_rate_cents');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'hourly_rate_cents')) {
                // if you ever roll back, recreate it as nullable so it won't break again
                $table->integer('hourly_rate_cents')->nullable();
            }
        });
    }
};
