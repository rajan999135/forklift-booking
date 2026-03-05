<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('forklifts', function (Blueprint $table) {
            // If these columns might already exist in some env, guard with exists checks if you use doctrine/dbal.
            if (!Schema::hasColumn('forklifts', 'capacity_kg')) {
                $table->unsignedInteger('capacity_kg')->nullable()->after('name');
            }
            if (!Schema::hasColumn('forklifts', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->default(75)->after('capacity_kg');
            }
            if (!Schema::hasColumn('forklifts', 'image')) {
                $table->string('image')->nullable()->after('hourly_rate');
            }
            if (!Schema::hasColumn('forklifts', 'images')) {
                $table->json('images')->nullable()->after('image');
            }
            // Optional, only if you plan to filter by location
            if (!Schema::hasColumn('forklifts', 'location_id')) {
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete()->after('images');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forklifts', function (Blueprint $table) {
            // drop FKs first if present
            if (Schema::hasColumn('forklifts', 'location_id')) {
                $table->dropConstrainedForeignId('location_id');
            }
            $table->dropColumn(['capacity_kg', 'hourly_rate', 'image', 'images']);
        });
    }
};
