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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('short_description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('slug')->unique()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('location')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');
            
            // Add age_categories JSON field for flexible age-based pricing
            $table->json('age_categories')->nullable();
            
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->decimal('full_price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('child_price', 10, 2)->nullable();
            $table->decimal('infant_price', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->boolean('deposit_required')->default(false);
            $table->integer('deposit_percentage')->nullable();
            $table->json('included')->nullable();
            $table->json('excluded')->nullable();
            $table->string('duration')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('group_size')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->json('languages')->nullable();
            $table->string('ticket')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->json('amenities')->nullable();
            $table->json('facilities')->nullable();
            $table->json('rules')->nullable();
            $table->json('safety')->nullable();
            $table->json('cancellation_policy')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('show_on_homepage')->default(false);
            $table->boolean('status')->default(true);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_per_person')->default(false);
            $table->integer('views')->default(0);
            $table->string('video_url')->nullable();
            $table->text('address')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->onDelete('set null');
            
            // Date range fields for listings
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            
            // UI fields
            $table->string('tour_plan_sub_title')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('google_map_sub_title')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('google_map_url')->nullable();
            $table->integer('room_count')->default(1);
            $table->integer('adult_count')->default(1);
            $table->integer('children_count')->default(0);
            
            $table->timestamps();
        });
        
        // Ensure the table uses utf8mb4 charset
        DB::statement('ALTER TABLE services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
}; 