<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The unique slot index was never successfully created in the DB
        // so there is nothing to drop. This migration is a no-op.
    }

    public function down(): void
    {
        // Do not recreate the unique constraint — it breaks cancelled slot reuse
    }
};