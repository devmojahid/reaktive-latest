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
            // Add age_categories column if it doesn't exist
            if (!Schema::hasColumn('services', 'age_categories')) {
                $table->json('age_categories')->nullable()->after('infant_price')->comment('Age-specific pricing configuration: infant, baby, child, adult');
            }
            
            // Add is_new column if it doesn't exist
            if (!Schema::hasColumn('services', 'is_new')) {
                $table->boolean('is_new')->default(false)->after('status');
            }
            
            // Add is_per_person column if it doesn't exist
            if (!Schema::hasColumn('services', 'is_per_person')) {
                $table->boolean('is_per_person')->default(true)->after('is_new');
            }
            
            // Add additional fields if they don't exist
            if (!Schema::hasColumn('services', 'google_map_sub_title')) {
                $table->string('google_map_sub_title')->nullable()->after('website');
            }
            
            if (!Schema::hasColumn('services', 'google_map_url')) {
                $table->text('google_map_url')->nullable()->after('google_map_sub_title');
            }
            
            if (!Schema::hasColumn('services', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->after('service_type_id')->constrained('destinations')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('services', 'check_in_date')) {
                $table->date('check_in_date')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('services', 'check_out_date')) {
                $table->date('check_out_date')->nullable()->after('check_in_date');
            }
            
            if (!Schema::hasColumn('services', 'tour_plan_sub_title')) {
                $table->string('tour_plan_sub_title')->nullable()->after('check_out_date');
            }
            
            if (!Schema::hasColumn('services', 'room_count')) {
                $table->integer('room_count')->nullable()->after('tour_plan_sub_title');
            }
            
            if (!Schema::hasColumn('services', 'adult_count')) {
                $table->integer('adult_count')->nullable()->after('room_count');
            }
            
            if (!Schema::hasColumn('services', 'children_count')) {
                $table->integer('children_count')->nullable()->after('adult_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $columnsToCheck = [
                'age_categories', 'is_new', 'is_per_person', 'google_map_sub_title', 
                'google_map_url', 'destination_id', 'check_in_date', 'check_out_date',
                'tour_plan_sub_title', 'room_count', 'adult_count', 'children_count'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 