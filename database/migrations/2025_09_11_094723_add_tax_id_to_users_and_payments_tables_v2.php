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
        if (!Schema::hasColumn('users', 'tax_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('tax_id', 64)->nullable()->after('email');
            });
        }
        if (!Schema::hasColumn('payments', 'tax_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('tax_id')->nullable()->after('customer_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'tax_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tax_id');
            });
        }
        if (Schema::hasColumn('payments', 'tax_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('tax_id');
            });
        }
    }
};
