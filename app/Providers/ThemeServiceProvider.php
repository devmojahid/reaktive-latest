<?php

namespace App\Providers;

use App\Themes\Core\Theme;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the theme service
        $this->app->singleton('theme', function ($app) {
            $theme = new Theme();
            $active = $theme->getActive();
            return $theme->set($active);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/themes.php', 'themes'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Create a theme() helper function
        if (!function_exists('theme')) {
            function theme() {
                return app('theme');
            }
        }

        // Add theme views to the views path
        $theme = app('theme');
        
        if ($theme->exists($theme->current())) {
            $themePath = $theme->getThemePath($theme->current());
            
            // Add theme views directory
            View::addLocation($themePath . '/views');
            
            // Add theme functions file if it exists
            $functionsFile = $themePath . '/functions/functions.php';
            if (File::exists($functionsFile)) {
                require_once $functionsFile;
            }
            
            // Add theme partials directory to Blade component namespace
            Blade::componentNamespace('Theme\\' . ucfirst($theme->current()) . '\\Components', 'theme');
            
            // Register theme routes
            $routesFile = $themePath . '/routes.php';
            if (File::exists($routesFile)) {
                Route::middleware('web')
                    ->namespace('Theme\\' . ucfirst($theme->current()) . '\\Controllers')
                    ->name('theme.' . $theme->current() . '.')
                    ->group($routesFile);
            }
        }
        
        // Add theme asset Blade directive
        Blade::directive('themeasset', function ($expression) {
            return "<?php echo theme()->asset($expression); ?>";
        });
        
        // Add theme content Blade directive
        Blade::directive('themecontent', function ($expression) {
            return "<?php echo theme()->getContent($expression); ?>";
        });
        
        // Add theme translation Blade directive
        Blade::directive('themetrans', function ($expression) {
            return "<?php echo theme_trans($expression); ?>";
        });
        
        // Add theme breadcrumb Blade directive
        Blade::directive('themebreadcrumbs', function () {
            return "<?php echo theme_breadcrumbs(); ?>";
        });
    }
} 