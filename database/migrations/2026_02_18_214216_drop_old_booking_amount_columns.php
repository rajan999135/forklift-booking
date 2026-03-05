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
        $table->dropColumn([
            'subtotal_cents',
            'gst_cents',
            'pst_cents',
            'total_cents',
            'stripe_pi',
            'invoice_no',
        ]);
    });
}

public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->integer('subtotal_cents')->default(0);
        $table->integer('gst_cents')->default(0);
        $table->integer('pst_cents')->default(0);
        $table->integer('total_cents')->default(0);
        $table->string('stripe_pi')->nullable();
        $table->string('invoice_no')->nullable();
    });
}
};
