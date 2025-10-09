<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First convert the entire table to utf8mb4 
        DB::statement('ALTER TABLE services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        // Then specifically update text columns that may contain special characters
        Schema::table('services', function (Blueprint $table) {
            // Update text columns to explicitly use utf8mb4 with proper collation
            $table->string('title')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->text('short_description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('slug')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('location')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('duration')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('group_size')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('ticket')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->text('address')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
        });

        // Add missing columns if they don't exist
        if (!Schema::hasColumn('services', 'age_categories')) {
            Schema::table('services', function (Blueprint $table) {
                $table->json('age_categories')->nullable()->after('service_type_id');
            });
        }

        if (!Schema::hasColumn('services', 'destination_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreignId('destination_id')->nullable()->after('user_id')->constrained('destinations')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('services', 'is_new')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('is_new')->default(false)->after('status');
            });
        }

        if (!Schema::hasColumn('services', 'is_per_person')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('is_per_person')->default(false)->after('is_new');
            });
        }

        if (!Schema::hasColumn('services', 'tour_plan_sub_title')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('tour_plan_sub_title')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->after('social_links');
            });
        }

        if (!Schema::hasColumn('services', 'google_map_sub_title')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('google_map_sub_title')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->after('tour_plan_sub_title');
            });
        }

        if (!Schema::hasColumn('services', 'google_map_url')) {
            Schema::table('services', function (Blueprint $table) {
                $table->text('google_map_url')->nullable()->after('google_map_sub_title');
            });
        }

        if (!Schema::hasColumn('services', 'room_count')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('room_count');
            });
        }

        if (!Schema::hasColumn('services', 'adult_count')) {
            Schema::table('services', function (Blueprint $table) {
                $table->integer('adult_count')->default(1)->after('google_map_url');
            });
        }

        if (!Schema::hasColumn('services', 'children_count')) {
            Schema::table('services', function (Blueprint $table) {
                $table->integer('children_count')->default(0)->after('adult_count');
            });
        }

        // Remove check_in_time and check_out_time columns if they exist
        if (Schema::hasColumn('services', 'check_in_time')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('check_in_time');
            });
        }

        if (Schema::hasColumn('services', 'check_out_time')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('check_out_time');
            });
        }

        // Ensure check_in_date and check_out_date are date fields (not time)
        if (Schema::hasColumn('services', 'check_in_date')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('check_in_date');
            });
        }

        if (Schema::hasColumn('services', 'check_out_date')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('check_out_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This is a data-safe migration, we won't reverse charset changes
        // as they might cause data loss for UTF-8 characters
        
        // Just add back the removed columns if needed

        if (!Schema::hasColumn('services', 'room_count')) {
            Schema::table('services', function (Blueprint $table) {
                $table->integer('room_count')->default(1);
            });
        }

        if (!Schema::hasColumn('services', 'check_in_time')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('check_in_time')->nullable();
            });
        }

        if (!Schema::hasColumn('services', 'check_out_time')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('check_out_time')->nullable();
            });
        }

        if (!Schema::hasColumn('services', 'check_in_date')) {
            Schema::table('services', function (Blueprint $table) {
                $table->date('check_in_date')->nullable();
            });
        }

        if (!Schema::hasColumn('services', 'check_out_date')) {
            Schema::table('services', function (Blueprint $table) {
                $table->date('check_out_date')->nullable();
            });
        }
    }
};
