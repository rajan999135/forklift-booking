<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $t) {

            if (!Schema::hasColumn('bookings', 'service_address')) {
                $t->string('service_address')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'postal_code')) {
                $t->string('postal_code', 16)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $t->enum('payment_method', ['cash', 'card'])->default('cash');
            }

            if (!Schema::hasColumn('bookings', 'payment_intent_id')) {
                $t->string('payment_intent_id')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'amount_subtotal')) {
                $t->integer('amount_subtotal')->default(0);
            }

            if (!Schema::hasColumn('bookings', 'amount_gst')) {
                $t->integer('amount_gst')->default(0);
            }

            if (!Schema::hasColumn('bookings', 'amount_pst')) {
                $t->integer('amount_pst')->default(0);
            }

            if (!Schema::hasColumn('bookings', 'amount_total')) {
                $t->integer('amount_total')->default(0);
            }

            if (!Schema::hasColumn('bookings', 'currency')) {
                $t->string('currency', 3)->default('CAD');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $t) {
            if (Schema::hasColumn('bookings', 'service_address')) $t->dropColumn('service_address');
            if (Schema::hasColumn('bookings', 'postal_code')) $t->dropColumn('postal_code');
            if (Schema::hasColumn('bookings', 'payment_method')) $t->dropColumn('payment_method');
            if (Schema::hasColumn('bookings', 'payment_intent_id')) $t->dropColumn('payment_intent_id');
            if (Schema::hasColumn('bookings', 'amount_subtotal')) $t->dropColumn('amount_subtotal');
            if (Schema::hasColumn('bookings', 'amount_gst')) $t->dropColumn('amount_gst');
            if (Schema::hasColumn('bookings', 'amount_pst')) $t->dropColumn('amount_pst');
            if (Schema::hasColumn('bookings', 'amount_total')) $t->dropColumn('amount_total');
            if (Schema::hasColumn('bookings', 'currency')) $t->dropColumn('currency');
        });
    }
};
