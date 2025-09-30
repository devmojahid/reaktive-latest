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
        // Only create if it doesn't already exist
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique()->index();
                $table->text('value')->nullable();
                $table->timestamps();
            });
            
            // Insert the default theme
            $activeTheme = 'theme1'; // Default theme
            
            // Try to get active theme from file if it exists
            $settingsPath = storage_path('app/theme_settings.json');
            if (file_exists($settingsPath)) {
                $settings = json_decode(file_get_contents($settingsPath), true);
                if (isset($settings['active_theme'])) {
                    $activeTheme = $settings['active_theme'];
                }
            }
            
            // Insert into database
            DB::table('settings')->insert([
                'key' => 'active_theme',
                'value' => $activeTheme,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the settings table if it existed before
        // and had other settings, so we'll just remove our theme setting
        DB::table('settings')->where('key', 'active_theme')->delete();
    }
};
