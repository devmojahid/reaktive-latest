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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('pickup_point_id')->nullable()->after('is_per_person');
            $table->decimal('pickup_charge', 10, 2)->default(0)->after('pickup_point_id');
            $table->string('pickup_point_name')->nullable()->after('pickup_charge');
            
            $table->foreign('pickup_point_id')->references('id')->on('pickup_points')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['pickup_point_id']);
            $table->dropColumn([
                'pickup_point_id',
                'pickup_charge',
                'pickup_point_name'
            ]);
        });
    }
};