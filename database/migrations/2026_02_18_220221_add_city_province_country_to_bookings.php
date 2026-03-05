<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'city')) {
                $table->string('city', 100)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('bookings', 'province')) {
                $table->string('province', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('bookings', 'country')) {
                $table->string('country', 100)->nullable()->after('province');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['city', 'province', 'country']);
        });
    }
};