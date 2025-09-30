<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ThemeSwitcherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if theme switching is enabled
        if (!config('themes.enable_switching', true)) {
            return $next($request);
        }

        // Handle theme switching if specified in request
        if ($request->has('theme')) {
            $requestedTheme = $request->theme;
            
            // Get available themes
            $availableThemes = config('themes.themes', []);
            $themeKeys = array_keys($availableThemes);
            
            // Check if the requested theme exists
            if (in_array($requestedTheme, $themeKeys) && 
                $this->themeExists($requestedTheme)) {
                // Set for current session only (preview mode)
                Session::put('selected_theme', $requestedTheme);
            }
        }
        
        // Get the theme from database first (the system default)
        $databaseTheme = null;
        try {
            $settingsRow = DB::table('settings')->where('key', 'active_theme')->first();
            if ($settingsRow) {
                $databaseTheme = $settingsRow->value;
            }
        } catch (\Exception $e) {
            // If there's a database error, continue with session or default
        }
        
        // Priority order for theme selection:
        // 1. Session (temporary preview)
        // 2. Database (system default)
        // 3. Config (fallback)
        $selectedTheme = Session::get(
            'selected_theme', 
            $databaseTheme ?? config('themes.default', 'theme1')
        );
        
        // Verify the theme exists, fallback to default if not
        if (!$this->themeExists($selectedTheme)) {
            $selectedTheme = config('themes.default', 'theme1');
        }
        
        // Set the current theme
        app('theme')->set($selectedTheme);
        
        return $next($request);
    }
    
    /**
     * Check if a theme exists with case-insensitive path checking
     * 
     * @param string $theme
     * @return bool
     */
    protected function themeExists($theme)
    {
        // Try different case variations of the path
        $possiblePaths = [
            base_path("Cms/themes/{$theme}"),
            base_path("cms/themes/{$theme}"),
            base_path("CMS/themes/{$theme}")
        ];
        
        foreach ($possiblePaths as $path) {
            if (File::exists($path) && File::isDirectory($path)) {
                return true;
            }
        }
        
        return false;
    }
} 