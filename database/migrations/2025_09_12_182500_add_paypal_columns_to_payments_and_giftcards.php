<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payments table
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'provider')) {
                $table->string('provider', 16)->default('stripe')->after('course_id');
            }
            if (!Schema::hasColumn('payments', 'paypal_order_id')) {
                $table->string('paypal_order_id')->nullable()->after('stripe_payment_intent_id')->index();
            }
            if (!Schema::hasColumn('payments', 'paypal_capture_id')) {
                $table->string('paypal_capture_id')->nullable()->after('paypal_order_id')->index();
            }
        });

        // Gift cards table
        Schema::table('gift_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('gift_cards', 'provider')) {
                $table->string('provider', 16)->default('stripe')->after('currency');
            }
            if (!Schema::hasColumn('gift_cards', 'paypal_order_id')) {
                $table->string('paypal_order_id')->nullable()->after('stripe_payment_intent_id')->index();
            }
            if (!Schema::hasColumn('gift_cards', 'paypal_capture_id')) {
                $table->string('paypal_capture_id')->nullable()->after('paypal_order_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'paypal_capture_id')) {
                $table->dropColumn('paypal_capture_id');
            }
            if (Schema::hasColumn('payments', 'paypal_order_id')) {
                $table->dropColumn('paypal_order_id');
            }
            if (Schema::hasColumn('payments', 'provider')) {
                $table->dropColumn('provider');
            }
        });

        Schema::table('gift_cards', function (Blueprint $table) {
            if (Schema::hasColumn('gift_cards', 'paypal_capture_id')) {
                $table->dropColumn('paypal_capture_id');
            }
            if (Schema::hasColumn('gift_cards', 'paypal_order_id')) {
                $table->dropColumn('paypal_order_id');
            }
            if (Schema::hasColumn('gift_cards', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }
};
