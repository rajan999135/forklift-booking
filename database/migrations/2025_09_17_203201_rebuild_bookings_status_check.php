<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only run if the bookings table exists and we're on MySQL
        if (!Schema::hasTable('bookings')) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            // On SQLite/Postgres etc, skip (or implement a different strategy if needed)
            return;
        }

        // Expand allowed statuses to include 'awaiting_admin'
        DB::statement("
            ALTER TABLE bookings
            MODIFY COLUMN status ENUM('pending','awaiting_admin','confirmed','cancelled') NOT NULL
        ");
    }

    public function down(): void
    {
        // Only run if the bookings table exists and we're on MySQL
        if (!Schema::hasTable('bookings')) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        // Revert to original enum without 'awaiting_admin'
        DB::statement("
            ALTER TABLE bookings
            MODIFY COLUMN status ENUM('pending','confirmed','cancelled') NOT NULL
        ");
    }
};