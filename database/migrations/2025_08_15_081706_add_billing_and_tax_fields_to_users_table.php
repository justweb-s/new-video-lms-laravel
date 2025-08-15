<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tax_code', 32)->nullable()->after('last_login');
            $table->string('tax_id', 64)->nullable()->after('tax_code');
            $table->string('billing_address_line1')->nullable()->after('tax_id');
            $table->string('billing_address_line2')->nullable()->after('billing_address_line1');
            $table->string('billing_city')->nullable()->after('billing_address_line2');
            $table->string('billing_state')->nullable()->after('billing_city');
            $table->string('billing_postal_code', 32)->nullable()->after('billing_state');
            $table->string('billing_country', 2)->nullable()->after('billing_postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tax_code',
                'tax_id',
                'billing_address_line1',
                'billing_address_line2',
                'billing_city',
                'billing_state',
                'billing_postal_code',
                'billing_country',
            ]);
        });
    }
};
