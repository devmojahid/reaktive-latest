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
        Schema::table('services', function (Blueprint $table) {
            $table->string('tour_plan_sub_title')->nullable();
            $table->string('google_map_sub_title')->nullable();
            $table->text('google_map_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('tour_plan_sub_title');
            $table->dropColumn('google_map_sub_title');
            $table->dropColumn('google_map_url');
        });
    }
};
