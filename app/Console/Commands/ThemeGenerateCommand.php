<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ThemeGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:generate {name} {--force : Force overwrite existing theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new theme';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        // Convert to lowercase and remove spaces
        $name = Str::lower(str_replace(' ', '', $name));

        // Create theme directory
        $themeDir = base_path("cms/themes/{$name}");

        if (File::exists($themeDir) && !$force) {
            $this->error("Theme '{$name}' already exists. Use --force to overwrite.");
            return 1;
        }

        // Create directories
        $directories = [
            '',
            '/assets',
            '/assets/css',
            '/assets/js',
            '/assets/img',
            '/functions',
            '/lang',
            '/views',
            '/views/layouts',
            '/views/partials',
            '/views/pages',
        ];

        foreach ($directories as $dir) {
            if (!File::exists($themeDir . $dir)) {
                File::makeDirectory($themeDir . $dir, 0755, true);
            }
        }

        // Create theme.json
        $themeJson = [
            'id' => "trips/{$name}",
            'name' => Str::title($name),
            'namespace' => 'Theme\\' . Str::studly($name) . '\\',
            'author' => 'Trips Developer',
            'url' => null,
            'version' => '1.0.0',
            'description' => "The {$name} theme for Trips platform",
            'required_plugins' => []
        ];

        File::put(
            $themeDir . '/theme.json',
            json_encode($themeJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // Create config.php
        $configContent = <<<'PHP'
<?php

return [
    'inherit' => null,

    'events' => [
        'beforeRenderTheme' => function ($theme) {
            // Add theme assets
            $theme->addStyle('css/theme.css');
            $theme->addScript('js/theme.js', [], 'footer');
        },
    ],
];
PHP;

        File::put($themeDir . '/config.php', $configContent);

        // Create functions.php
        $functionsContent = <<<PHP
<?php

/**
 * Theme specific functions for {$name}
 */

/**
 * Get theme information
 */
function get_theme_info()
{
    return theme()->loadThemeInfo(theme()->current());
}

/**
 * Format currency based on site settings
 */
function {$name}_format_currency(\$amount)
{
    \$currency_icon = Session::get('currency_icon', '\$');
    \$currency_position = Session::get('currency_position', 'left');

    if (\$currency_position == 'left') {
        return \$currency_icon . number_format(\$amount, 2);
    } else {
        return number_format(\$amount, 2) . \$currency_icon;
    }
}

/**
 * Get theme version
 */
function {$name}_get_version()
{
    return '1.0.0';
}
PHP;

        File::put($themeDir . '/functions/functions.php', $functionsContent);

        // Create routes.php
        $routesContent = <<<PHP
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Theme Routes
|--------------------------------------------------------------------------
|
| Here is where you can register theme-specific routes for your theme.
| These routes are loaded by the ThemeServiceProvider within a theme group which
| contains the "web" middleware. Now create something great!
|
*/

// Theme-specific pages
Route::get('/{$name}/welcome', '{$name}Controller@welcome')->name('welcome');
Route::get('/{$name}/about', '{$name}Controller@about')->name('about');
Route::get('/{$name}/contact', '{$name}Controller@contact')->name('contact');
PHP;

        File::put($themeDir . '/routes.php', $routesContent);

        // Create basic language file
        $langContent = <<<'PHP'
<?php

return [
    // General
    'site_name' => 'Trips',
    'tagline' => 'Your Premier Tour Experience Platform',

    // Navigation
    'nav_home' => 'Home',
    'nav_about' => 'About',
    'nav_courses' => 'Courses',
    'nav_blog' => 'Blog',
    'nav_contact' => 'Contact',
    'nav_login' => 'Login',

    // Home Page
    'hero_title' => 'Welcome to Trips',
    'hero_subtitle' => 'Your premier tour experience platform',
    'hero_button' => 'Explore Courses',

    // Theme Switcher
    'theme_switcher_label' => 'Theme:',
    'theme_light' => 'Light',
    'theme_dark' => 'Dark',
];
PHP;

        File::put($themeDir . '/lang/en.php', $langContent);

        // Create theme-switcher.blade.php
        $themeSwitcherContent = <<<'PHP'
<div class="theme-switcher">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="theme-options">
                <span>@themetrans('theme_switcher_label') </span>
                <a href="/?theme=theme1" class="{{ Theme::current() == 'theme1' ? 'active' : '' }}">@themetrans('theme_light')</a> |
                <a href="/?theme=theme2" class="{{ Theme::current() == 'theme2' ? 'active' : '' }}">@themetrans('theme_dark')</a>
            </div>
        </div>
    </div>
</div>

<style>
    .theme-switcher {
        padding: 5px 0;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .theme-options {
        font-size: 14px;
    }

    .theme-options a {
        margin: 0 5px;
        color: #6c757d;
        text-decoration: none;
    }

    .theme-options a.active {
        font-weight: bold;
        color: #0d6efd;
    }
</style>
PHP;

        File::put($themeDir . '/views/partials/theme-switcher.blade.php', $themeSwitcherContent);

        // Create basic CSS
        $cssContent = <<<'CSS'
/* Basic theme styling */
:root {
    --primary-color: #3490dc;
    --secondary-color: #38c172;
    --dark-color: #343a40;
    --light-color: #f8f9fa;
}

body {
    font-family: 'Arial', sans-serif;
    line-height: 1.6;
}

.hero-section {
    background-color: var(--primary-color);
    color: white;
    padding: 100px 0;
    text-align: center;
}

.footer {
    background-color: var(--dark-color);
    color: white;
    padding: 50px 0 20px;
}
CSS;

        File::put($themeDir . '/assets/css/theme.css', $cssContent);

        // Create basic JS
        $jsContent = <<<'JS'
// Theme functions
document.addEventListener('DOMContentLoaded', function() {
    console.log('Theme loaded successfully!');
});
JS;

        File::put($themeDir . '/assets/js/theme.js', $jsContent);

        // Create basic index view
        $indexContent = <<<'PHP'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seo_setting->title ?? 'Trips' }}</title>
    <meta name="description" content="{{ $seo_setting->description ?? 'Trips platform' }}">
    <meta name="keywords" content="{{ $seo_setting->keywords ?? 'trips, theme, travel' }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('backend/img/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ theme()->asset('css/theme.css') }}">
</head>
<body>
    <!-- Theme Switcher -->
    @include('theme::partials.theme-switcher')

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">{{ $seo_setting->site_name ?? 'Trips' }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">@themetrans('nav_home')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about-us">@themetrans('nav_about')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/courses">@themetrans('nav_courses')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/blogs">@themetrans('nav_blog')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact-us">@themetrans('nav_contact')</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="/user/login">@themetrans('nav_login')</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4">@themetrans('hero_title')</h1>
                    <p class="lead">@themetrans('hero_subtitle')</p>
                    <a href="/courses" class="btn btn-light btn-lg mt-3">@themetrans('hero_button')</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">Main Content Area</h2>
                <p class="lead text-center">This is a starter template for your new theme.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4>About Trips</h4>
                    <p>Trips is your premier tour experience platform, offering high-quality courses and expert guidance.</p>
                </div>
                <div class="col-md-4">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-light">Home</a></li>
                        <li><a href="/about-us" class="text-light">About Us</a></li>
                        <li><a href="/courses" class="text-light">Courses</a></li>
                        <li><a href="/blogs" class="text-light">Blog</a></li>
                        <li><a href="/contact-us" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Contact Us</h4>
                    <address>
                        <p><i class="fas fa-map-marker-alt me-2"></i> 123 Education Street, Learning City</p>
                        <p><i class="fas fa-phone me-2"></i> +1 234 567 8901</p>
                        <p><i class="fas fa-envelope me-2"></i> info@trips.com</p>
                    </address>
                </div>
            </div>
            <hr class="bg-light">
            <div class="row">
                <div class="col-12 text-center">
                    <p>&copy; {{ date('Y') }} Trips. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme JS -->
    <script src="{{ theme()->asset('js/theme.js') }}"></script>
</body>
</html>
PHP;

        File::put($themeDir . '/views/index.blade.php', $indexContent);

        // Update config/themes.php
        $themesConfig = include(config_path('themes.php'));
        $themesConfig['themes'][$name] = [
            'name' => Str::title($name),
            'description' => "The {$name} theme for Trips platform",
        ];

        $configContent = "<?php\n\nreturn " . var_export($themesConfig, true) . ";\n";
        File::put(config_path('themes.php'), $configContent);

        $this->info("Theme '{$name}' created successfully!");

        return 0;
    }
}