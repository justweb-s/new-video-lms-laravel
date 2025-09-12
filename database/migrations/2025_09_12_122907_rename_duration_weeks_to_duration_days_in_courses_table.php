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
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('duration_weeks', 'duration_days');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('duration_days')->default(30)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('duration_days')->default(null)->change();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('duration_days', 'duration_weeks');
        });
    }
};
