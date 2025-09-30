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
         Schema::table('wishlists', function (Blueprint $table) {
            $table->unsignedBigInteger('wishable_id')->nullable()->after('user_id');
            $table->string('wishable_type')->nullable()->after('wishable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropColumn('wishable_id');
            $table->dropColumn('wishable_type');
        });
    }
};
