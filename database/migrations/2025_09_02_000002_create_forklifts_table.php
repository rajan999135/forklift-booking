<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forklifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('capacity_kg');
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['available','unavailable','maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forklifts');
    }
};
