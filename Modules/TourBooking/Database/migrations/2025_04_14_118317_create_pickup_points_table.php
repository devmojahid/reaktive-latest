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
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('extra_charge', 10, 2)->nullable()->comment('Optional extra charge for this pickup point');
            $table->string('charge_type')->default('flat')->comment('flat, per_person, per_adult, per_child');
            $table->boolean('is_default')->default(false)->comment('Default pickup point for the service');
            $table->boolean('status')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index for performance
            $table->index(['service_id', 'status']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_points');
    }
};
