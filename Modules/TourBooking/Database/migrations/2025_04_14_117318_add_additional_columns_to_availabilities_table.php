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
        Schema::table('availabilities', function (Blueprint $table) {
            // Add per_children_price column if it doesn't exist
            if (!Schema::hasColumn('availabilities', 'per_children_price')) {
                $table->decimal('per_children_price', 10, 2)->nullable()->after('special_price')->comment('Override default child price for this date');
            }
            
            // Add age_categories column if it doesn't exist
            if (!Schema::hasColumn('availabilities', 'age_categories')) {
                $table->json('age_categories')->nullable()->after('per_children_price')->comment('Age-specific pricing and capacity for this availability slot');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            if (Schema::hasColumn('availabilities', 'per_children_price')) {
                $table->dropColumn('per_children_price');
            }
            if (Schema::hasColumn('availabilities', 'age_categories')) {
                $table->dropColumn('age_categories');
            }
        });
    }
}; 