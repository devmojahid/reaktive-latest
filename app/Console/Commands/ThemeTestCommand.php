<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Facades\Theme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThemeTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:test {action? : Action to perform (list|check|fix)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and debug theme management system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action') ?? 'list';
        
        switch ($action) {
            case 'list':
                $this->listThemes();
                break;
            
            case 'check':
                $this->checkActiveTheme();
                break;
            
            case 'fix':
                $this->fixThemeStorage();
                break;
            
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
        
        return 0;
    }
    
    /**
     * List all available themes
     */
    protected function listThemes()
    {
        $this->info("Checking theme directories...");
        
        // Check possible theme directories
        $possiblePaths = [
            base_path('Cms/themes'),
            base_path('cms/themes'),
            base_path('CMS/themes')
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->info("✓ Path exists: {$path}");
                if (is_dir($path)) {
                    $this->info("✓ Path is a directory");
                    $dirContent = scandir($path);
                    $this->info("Directory contents: " . implode(', ', array_filter($dirContent, function($item) {
                        return !in_array($item, ['.', '..']);
                    })));
                } else {
                    $this->error("✕ Path is not a directory: {$path}");
                }
            } else {
                $this->error("✕ Path does not exist: {$path}");
            }
        }
        
        $this->info("=== Theme System Check ===");
        $this->info("Checking available themes...");
        
        $themes = Theme::all();
        
        if (empty($themes)) {
            $this->error("No themes found!");
            return;
        }
        
        $headers = ['Name', 'Directory', 'Version', 'Author', 'Description'];
        $rows = [];
        
        foreach ($themes as $name => $info) {
            $rows[] = [
                $name,
                Theme::getThemePath($name),
                $info['version'] ?? 'N/A',
                $info['author'] ?? 'N/A',
                $info['description'] ?? 'N/A',
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    /**
     * Check active theme configuration
     */
    protected function checkActiveTheme()
    {
        $this->info("=== Active Theme Configuration ===");
        
        // Check database
        try {
            $settingsRow = DB::table('settings')->where('key', 'active_theme')->first();
            if ($settingsRow) {
                $this->info("Database setting: {$settingsRow->value}");
            } else {
                $this->warn("No active_theme setting in database");
            }
        } catch (\Exception $e) {
            $this->error("Database error: " . $e->getMessage());
        }
        
        // Check file-based setting
        $settingsPath = storage_path('app/theme_settings.json');
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            if (isset($settings['active_theme'])) {
                $this->info("File setting: {$settings['active_theme']}");
            } else {
                $this->warn("No active_theme in settings file");
            }
        } else {
            $this->warn("Settings file does not exist: {$settingsPath}");
        }
        
        // Check Theme facade
        $currentTheme = Theme::current();
        $this->info("Current theme from facade: {$currentTheme}");
        
        $activeTheme = Theme::getActive();
        $this->info("Active theme from facade: {$activeTheme}");
        
        // Check existence of theme directory
        $themePath = Theme::getThemePath($activeTheme);
        if (file_exists($themePath)) {
            $this->info("✓ Theme directory exists: {$themePath}");
        } else {
            $this->error("✕ Theme directory does not exist: {$themePath}");
        }
    }
    
    /**
     * Fix theme storage issues
     */
    protected function fixThemeStorage()
    {
        $this->info("=== Fixing Theme Storage ===");
        
        // Get a list of all theme directories
        $themeDirs = [];
        $possiblePaths = [
            base_path('Cms/themes'),
            base_path('cms/themes'),
            base_path('CMS/themes')
        ];
        
        foreach ($possiblePaths as $basePath) {
            if (file_exists($basePath) && is_dir($basePath)) {
                foreach (scandir($basePath) as $item) {
                    if (!in_array($item, ['.', '..']) && is_dir($basePath . '/' . $item)) {
                        $themeDirs[] = $item;
                    }
                }
                break; // Use the first valid path
            }
        }
        
        if (empty($themeDirs)) {
            $this->error("No theme directories found!");
            return;
        }
        
        $this->info("Found themes: " . implode(', ', $themeDirs));
        
        // Ask which theme to set as active
        $defaultTheme = 'theme1';
        $activeTheme = $this->choice(
            'Which theme should be set as active?',
            $themeDirs,
            array_search($defaultTheme, $themeDirs) !== false ? $defaultTheme : 0
        );
        
        // Update database
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'active_theme'],
                ['value' => $activeTheme, 'updated_at' => now()]
            );
            $this->info("✓ Updated database setting");
        } catch (\Exception $e) {
            $this->error("Database error: " . $e->getMessage());
            
            // Try creating the table
            try {
                if (!Schema::hasTable('settings')) {
                    Schema::create('settings', function ($table) {
                        $table->id();
                        $table->string('key')->unique();
                        $table->text('value')->nullable();
                        $table->timestamps();
                    });
                    
                    // Insert the theme setting
                    DB::table('settings')->insert([
                        'key' => 'active_theme',
                        'value' => $activeTheme,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $this->info("✓ Created settings table and inserted active_theme");
                }
            } catch (\Exception $innerEx) {
                $this->error("Failed to create settings table: " . $innerEx->getMessage());
            }
        }
        
        // Update settings file
        $settingsPath = storage_path('app/theme_settings.json');
        $settings = [];
        
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true) ?? [];
        }
        
        $settings['active_theme'] = $activeTheme;
        
        // Create directory if it doesn't exist
        $dir = storage_path('app');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
        $this->info("✓ Updated settings file");
        
        // Test the theme system
        Theme::set($activeTheme);
        $this->info("✓ Theme set to: " . Theme::current());
        
        $this->info("Theme fix completed successfully!");
    }
} 