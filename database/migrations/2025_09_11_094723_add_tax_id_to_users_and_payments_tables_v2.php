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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tax_id')->nullable()->after('email');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->string('tax_id')->nullable()->after('customer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tax_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('tax_id');
        });
    }
};
