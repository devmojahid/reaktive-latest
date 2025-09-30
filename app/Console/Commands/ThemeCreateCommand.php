<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ThemeCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     * command: php artisan theme:create {name} --author={author} --description={description}
     *
     * @var string
     */
    protected $signature = 'theme:create {name : The name of the theme}
                           {--author= : The author of the theme}
                           {--description= : The description of the theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new theme structure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $author = $this->option('author') ?: 'Anonymous';
        $description = $this->option('description') ?: 'A new theme for Trips';

        $themePath = base_path('cms/themes/' . $name);

        // Check if the theme already exists
        if (File::exists($themePath)) {
            $this->error("Theme '{$name}' already exists!");
            return 1;
        }

        // Create theme directory structure
        $this->createDirectories($themePath);

        // Create theme.json file
        $this->createThemeJson($themePath, $name, $author, $description);

        // Create config.php file
        $this->createConfigFile($themePath);

        // Create sample views
        $this->createViews($themePath);

        // Create functions file
        $this->createFunctionsFile($themePath);

        // Create a basic vite.config.js
        $this->createViteConfig($themePath);

        // Create basic assets
        $this->createBasicAssets($themePath);

        $this->info("Theme '{$name}' created successfully!");

        return 0;
    }

    /**
     * Create the directory structure for the theme.
     *
     * @param string $themePath
     * @return void
     */
    protected function createDirectories($themePath)
    {
        $directories = [
            $themePath,
            $themePath . '/assets',
            $themePath . '/assets/css',
            $themePath . '/assets/js',
            $themePath . '/assets/images',
            $themePath . '/functions',
            $themePath . '/layouts',
            $themePath . '/partials',
            $themePath . '/lang',
            $themePath . '/lang/en',
            $themePath . '/public',
            $themePath . '/public/css',
            $themePath . '/public/js',
            $themePath . '/public/images',
            $themePath . '/routes',
            $themePath . '/src',
            $themePath . '/views',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory($directory, 0755, true);
            $this->info("Created directory: {$directory}");
        }
    }

    /**
     * Create the theme.json file.
     *
     * @param string $themePath
     * @param string $name
     * @param string $author
     * @param string $description
     * @return void
     */
    protected function createThemeJson($themePath, $name, $author, $description)
    {
        $themeJson = [
            'id' => 'trips/' . $name,
            'name' => ucfirst($name),
            'namespace' => 'Theme\\' . ucfirst($name) . '\\',
            'author' => $author,
            'url' => null,
            'version' => '1.0.0',
            'description' => $description,
            'required_plugins' => []
        ];

        File::put(
            $themePath . '/theme.json',
            json_encode($themeJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // Also create a placeholder screenshot
        File::copy(
            public_path('backend/img/placeholder-image.jpg'),
            $themePath . '/screenshot.png'
        );

        $this->info("Created theme.json file");
    }

    /**
     * Create the config.php file.
     *
     * @param string $themePath
     * @return void
     */
    protected function createConfigFile($themePath)
    {
        $configContent = <<<'EOT'
<?php

return [
    'inherit' => null,

    'events' => [
        'beforeRenderTheme' => function ($theme) {
            // This is executed before the theme is rendered

            // Register stylesheets
            $theme->asset()->usePath()->add('theme-style', 'css/theme.css');

            // Register scripts
            $theme->asset()->container('footer')->usePath()->add('theme-script', 'js/theme.js');
        },
    ],
];
EOT;

        File::put($themePath . '/config.php', $configContent);
        $this->info("Created config.php file");
    }

    /**
     * Create sample views.
     *
     * @param string $themePath
     * @return void
     */
    protected function createViews($themePath)
    {
        // Create layout view
        $layoutContent = <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trips')</title>
    <meta name="description" content="@yield('description', 'Your Premier Tour Experience')">

    <!-- Theme Assets -->
    @stack('styles')
</head>
<body>
    <!-- Header -->
    @include('theme::partials.header')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('theme::partials.footer')

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
EOT;

        File::put($themePath . '/views/layouts/app.blade.php', $layoutContent);

        // Create index view
        $indexContent = <<<'EOT'
@extends('theme::layouts.app')

@section('title', 'Home - Trips')

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Welcome to Trips</h1>
                <p>Your Premier Tour Experience</p>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Featured Tours</h2>

                <!-- Featured tours would go here -->

            </div>
        </div>
    </div>
</div>
@endsection
EOT;

        File::put($themePath . '/views/index.blade.php', $indexContent);

        // Create header partial
        $headerContent = <<<'EOT'
<!-- Header -->
<header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="logo">
                    <a href="/">
                        <img src="{{ asset('themes/' . theme()->current() . '/public/images/logo.png') }}" alt="Trips Logo">
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <nav class="main-nav">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/about-us">About Us</a></li>
                        <li><a href="/blogs">Blog</a></li>
                        <li><a href="/contact-us">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
EOT;

        // Create directory if it doesn't exist
        File::makeDirectory($themePath . '/views/partials', 0755, true, true);

        File::put($themePath . '/views/partials/header.blade.php', $headerContent);

        // Create footer partial
        $footerContent = <<<'EOT'
<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h3>About Us</h3>
                <p>Trips is your premier tour experience provider.</p>
            </div>
            <div class="col-md-4">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/about-us">About Us</a></li>
                    <li><a href="/blogs">Blog</a></li>
                    <li><a href="/contact-us">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h3>Contact Us</h3>
                <p>Email: info@trips.com</p>
                <p>Phone: +1 234 567 8901</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="copyright">
                    <p>&copy; {{ date('Y') }} Trips. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
EOT;

        File::put($themePath . '/views/partials/footer.blade.php', $footerContent);

        $this->info("Created views");
    }

    /**
     * Create functions file.
     *
     * @param string $themePath
     * @return void
     */
    protected function createFunctionsFile($themePath)
    {
        $functionsContent = <<<'EOT'
<?php

/**
 * Theme functions file
 *
 * This file contains theme-specific functions
 */

/**
 * Get theme information
 */
function get_theme_info()
{
    return theme()->loadThemeInfo(theme()->current());
}

/**
 * Example function to demonstrate theme-specific functionality
 */
function theme_custom_function()
{
    return 'This is a custom function from the theme.';
}
EOT;

        File::put($themePath . '/functions/functions.php', $functionsContent);
        $this->info("Created functions file");
    }

    /**
     * Create vite config.
     *
     * @param string $themePath
     * @return void
     */
    protected function createViteConfig($themePath)
    {
        $viteConfigContent = <<<'EOT'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'cms/themes/themeName/assets/js/theme.js',
                'cms/themes/themeName/assets/css/theme.scss',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/cms/themes/themeName/public',
    },
});
EOT;

        $viteConfigContent = str_replace('themeName', $this->argument('name'), $viteConfigContent);

        File::put($themePath . '/vite.config.js', $viteConfigContent);
        $this->info("Created vite.config.js file");
    }

    /**
     * Create basic assets.
     *
     * @param string $themePath
     * @return void
     */
    protected function createBasicAssets($themePath)
    {
        // Create CSS file
        $cssContent = <<<'EOT'
/**
 * Theme Styles
 */

:root {
    --primary-color: #3490dc;
    --secondary-color: #38c172;
    --dark-color: #343a40;
    --light-color: #f8f9fa;
    --gray-color: #6c757d;
}

/* Base styles */
body {
    font-family: 'Arial', sans-serif;
    line-height: 1.6;
    color: #333;
}

.container {
    width: 100%;
    max-width: 1200px;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
}

/* Header */
.site-header {
    padding: 20px 0;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Hero section */
.hero-section {
    background-color: var(--primary-color);
    color: white;
    padding: 100px 0;
    text-align: center;
}

/* Footer */
.site-footer {
    background-color: var(--dark-color);
    color: white;
    padding: 50px 0 20px;
}

.site-footer h3 {
    color: white;
    margin-bottom: 20px;
}

.site-footer ul {
    list-style: none;
    padding: 0;
}

.site-footer ul li {
    margin-bottom: 10px;
}

.site-footer ul li a {
    color: #ccc;
    text-decoration: none;
}

.site-footer ul li a:hover {
    color: white;
}

.copyright {
    margin-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 20px;
    text-align: center;
}
EOT;

        File::put($themePath . '/assets/css/theme.css', $cssContent);

        // Create JS file
        $jsContent = <<<'EOT'
/**
 * Theme Scripts
 */

// On document ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Theme loaded successfully');

    // Example: Add mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });
    }
});
EOT;

        File::put($themePath . '/assets/js/theme.js', $jsContent);

        $this->info("Created basic assets");
    }
}