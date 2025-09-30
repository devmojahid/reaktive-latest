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
            $table->string('agency_name')->nullable();
            $table->string('agency_slug')->unique()->nullable();
            $table->string('agency_logo')->nullable();
            $table->string('website')->nullable();
            $table->string('location_map')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('agency_name');
            $table->dropColumn('agency_slug');
            $table->dropColumn('agency_logo');
            $table->dropColumn('website');
            $table->dropColumn('location_map');
        });
    }
};
