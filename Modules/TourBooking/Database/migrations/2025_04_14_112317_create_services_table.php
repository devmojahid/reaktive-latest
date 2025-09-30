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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');
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
            $table->string('duration')->nullable();
            $table->string('group_size')->nullable();
            $table->json('languages')->nullable();
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->string('ticket')->nullable();
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
            $table->integer('views')->default(0);
            $table->string('video_url')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
}; 