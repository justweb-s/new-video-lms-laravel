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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('billing_name')->nullable()->after('customer_details');
            $table->string('vat_number')->nullable()->after('billing_name'); // Partita IVA
            $table->string('billing_address_line1')->nullable()->after('vat_number');
            $table->string('billing_address_line2')->nullable()->after('billing_address_line1');
            $table->string('billing_address_city')->nullable()->after('billing_address_line2');
            $table->string('billing_address_state')->nullable()->after('billing_address_city'); // Provincia/Stato
            $table->string('billing_address_postal_code')->nullable()->after('billing_address_state');
            $table->string('billing_address_country')->nullable()->after('billing_address_postal_code'); // Codice ISO Paese (es. IT)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'billing_name',
                'vat_number',
                'billing_address_line1',
                'billing_address_line2',
                'billing_address_city',
                'billing_address_state',
                'billing_address_postal_code',
                'billing_address_country',
            ]);
        });
    }
};
