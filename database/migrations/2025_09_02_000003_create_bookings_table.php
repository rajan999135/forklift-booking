<?php
// database/migrations/2025_09_04_000000_create_bookings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('forklift_id')->constrained()->cascadeOnDelete();
            $t->string('location')->index();
            $t->dateTime('start_at')->index();
            $t->dateTime('end_at')->index();
            $t->string('address')->nullable();
            $t->string('postal_code', 10)->nullable();

            $t->enum('payment_method', ['cash','card']);
            $t->enum('status', ['pending','awaiting_admin','confirmed','cancelled'])->default('awaiting_admin');
            $t->string('stripe_pi')->nullable();        // PaymentIntent id
            $t->string('invoice_no')->nullable();       // human invoice id
            $t->unsignedInteger('hourly_rate_cents');   // store money in cents
            $t->unsignedInteger('subtotal_cents')->default(0);
            $t->unsignedInteger('gst_cents')->default(0);
            $t->unsignedInteger('pst_cents')->default(0);
            $t->unsignedInteger('total_cents')->default(0);

            $t->timestamps();
        });
        // Optional helper index to speed "overlap" checks
        Schema::table('bookings', function (Blueprint $t) {
            $t->index(['forklift_id','start_at','end_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
