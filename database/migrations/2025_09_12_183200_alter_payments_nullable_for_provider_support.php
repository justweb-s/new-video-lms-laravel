<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'course_id')) {
                $table->foreignId('course_id')->nullable()->change();
            }
            if (Schema::hasColumn('payments', 'stripe_session_id')) {
                $table->string('stripe_session_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'stripe_session_id')) {
                $table->string('stripe_session_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('payments', 'course_id')) {
                $table->foreignId('course_id')->nullable(false)->change();
            }
        });
    }
};
