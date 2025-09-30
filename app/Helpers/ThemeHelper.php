<?php

use Illuminate\Support\Facades\File;
use App\Models\Menu;
use App\Models\MenuItem;

/**
 * Get theme instance
 *
 * @return \App\Themes\Core\Theme
 */
if (!function_exists('theme')) {
    function theme() {
        return app('theme');
    }
}

/**
 * Get theme translation
 *
 * @param string $key
 * @param array $replace
 * @return string
 */
if (!function_exists('theme_trans')) {
    function theme_trans($key, $replace = []) {
        $theme = app('theme');
        $themePath = $theme->getThemePath($theme->current());
        $langPath = $themePath . '/lang';
        
        if (File::exists($langPath)) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale');
            
            $localePath = $langPath . '/' . $locale . '.php';
            $fallbackPath = $langPath . '/' . $fallbackLocale . '.php';
            
            if (File::exists($localePath)) {
                $translations = require $localePath;
                if (isset($translations[$key])) {
                    return strtr($translations[$key], $replace);
                }
            }
            
            if (File::exists($fallbackPath)) {
                $translations = require $fallbackPath;
                if (isset($translations[$key])) {
                    return strtr($translations[$key], $replace);
                }
            }
        }
        
        return $key;
    }
}

/**
 * Generate theme breadcrumbs
 *
 * @return string
 */
if (!function_exists('theme_breadcrumbs')) {
    function theme_breadcrumbs() {
        $theme = app('theme');
        $breadcrumbs = $theme->getBreadcrumbs();
        
        if (empty($breadcrumbs)) {
            return '';
        }
        
        $output = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            if ($index === count($breadcrumbs) - 1) {
                $output .= '<li class="breadcrumb-item active" aria-current="page">' . $breadcrumb['label'] . '</li>';
            } else {
                $output .= '<li class="breadcrumb-item"><a href="' . $breadcrumb['url'] . '">' . $breadcrumb['label'] . '</a></li>';
            }
        }
        
        $output .= '</ol></nav>';
        
        return $output;
    }
}

/**
 * Get theme content
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('theme_content')) {
    function theme_content($key, $default = null) {
        $theme = app('theme');
        $content = $theme->getContent($key);
        
        return $content ? $content : $default;
    }
}

/**
 * Display a navigation menu
 *
 * @param string|array $args Menu location or arguments
 * @return string HTML menu
 */
if (!function_exists('wp_nav_menu')) {
    function wp_nav_menu($args = []) {
        // Normalize arguments
        $defaults = [
            'theme_location' => '',
            'menu' => '',
            'container' => 'ul',
            'container_class' => '',
            'container_id' => '',
            'menu_class' => '',
            'menu_id' => '',
            'echo' => true,
            'fallback_cb' => '',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth' => 0,
            'walker' => null,
        ];

        if (is_string($args)) {
            $args = ['theme_location' => $args];
        }

        $args = array_merge($defaults, $args);
        
        // Find the menu
        $menu = null;
        
        // If a theme location is specified
        if (!empty($args['theme_location'])) {
            $menu = Menu::where('location', $args['theme_location'])
                  ->where('status', 1)
                  ->first();
        }
        
        // If a specific menu is specified by name or ID
        if (!$menu && !empty($args['menu'])) {
            if (is_numeric($args['menu'])) {
                $menu = Menu::where('id', $args['menu'])
                      ->where('status', 1)
                      ->first();
            } else {
                $menu = Menu::where('name', $args['menu'])
                      ->orWhere('slug', $args['menu'])
                      ->where('status', 1)
                      ->first();
            }
        }
        
        // If no menu was found
        if (!$menu) {
            if (is_callable($args['fallback_cb'])) {
                return call_user_func($args['fallback_cb'], $args);
            }
            return '';
        }
        
        // Build menu items
        $menu_items = $menu->activeRootItems()->with(['activeChildren'])->get();
        
        // Generate menu HTML
        $items_output = '';

        foreach ($menu_items as $item) {
            $items_output .= render_menu_item($item, $args);
        }
        
        // Wrap the menu items
        $menu_id = $args['menu_id'] ? $args['menu_id'] : 'menu-' . $menu->slug;
        $menu_class = $args['menu_class'] ? $args['menu_class'] : 'menu-' . $menu->slug . '-container';
        
        $output = sprintf(
            $args['items_wrap'],
            esc_attr($menu_id),
            esc_attr($menu_class),
            $items_output
        );
        
        // if ($args['echo']) {
        //     echo $output;
        // }
        
        return $output;
    }
}

/**
 * Render a menu item and its children
 *
 * @param MenuItem $item
 * @param array $args
 * @param int $depth
 * @return string
 */
if (!function_exists('render_menu_item')) {
    function render_menu_item($item, $args, $depth = 0) {
        $has_children = $item->hasActiveChildren();
        $item_classes = [];
        
        // Add classes for items with children
        if ($has_children) {
            $item_classes[] = 'menu-item-has-children';
            // if ($depth == 0) {
            //     $item_classes[] = 'dropdown';
            // } else {
            //     $item_classes[] = 'dropend';
            // }
        }
        
        // Custom CSS classes from the item
        if ($item->css_class) {
            $item_classes = array_merge($item_classes, explode(' ', $item->css_class));
        }
        
        // Current page detection (simple version)
        $current_url = url()->current();
        if ($item->url && ($current_url == $item->url || $current_url == url($item->url))) {
            $item_classes[] = 'current-menu-item';
            $item_classes[] = 'active';
        }
        
        $item_class_string = !empty($item_classes) ? ' class="' . esc_attr(implode(' ', $item_classes)) . '"' : '';
        
        // Build the menu item
        $item_output = '<li' . $item_class_string . '>';
        
        // Link attributes
        $atts = [
            'href' => !empty($item->url) ? $item->url : '#',
            'target' => !empty($item->target) ? $item->target : '',
            // 'class' => $has_children && $depth == 0 ? 'nav-link dropdown-toggle' : 'nav-link',
        ];
        
        if ($has_children && $depth == 0) {
            $atts['data-bs-toggle'] = 'dropdown';
            $atts['aria-expanded'] = 'false';
        }
        
        // Build the link element
        $attributes = '';
        foreach ($atts as $key => $value) {
            if (!empty($value)) {
                $attributes .= ' ' . $key . '="' . esc_attr($value) . '"';
            }
        }
        
        $title = $args['link_before'] . $item->title . $args['link_after'];
        $item_output .= $args['before'] . '<a' . $attributes . '>';
        
        // Add icon if exists
        if (!empty($item->icon_class)) {
            $item_output .= '<i class="' . esc_attr($item->icon_class) . '"></i> ';
        }
        
        $item_output .= $title . '</a>' . $args['after'];
        
        // If this item has children, append a submenu
        if ($has_children) {
            $sub_menu_classes = $depth == 0 ? 'sub-menu' : 'sub-menu';
            $item_output .= '<ul class="' . $sub_menu_classes . '">';
            
            $child_args = $args;
            $child_args['menu_class'] = 'sub-menu';
            
            foreach ($item->activeChildren as $child) {
                $item_output .= render_menu_item($child, $child_args, $depth + 1);
            }
            
            $item_output .= '</ul>';
        }
        
        $item_output .= '</li>';
        
        return $item_output;
    }
}

/**
 * Escape an HTML attribute
 */
if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Get all registered menu locations for the current theme
 *
 * @return array Menu locations
 */
if (!function_exists('get_registered_nav_menus')) {
    function get_registered_nav_menus() {
        $theme = app('theme');
        $themeInfo = $theme->loadThemeInfo($theme->current());
        
        return $themeInfo['menu_locations'] ?? [];
    }
}

/**
 * Check if a menu location has a menu assigned
 *
 * @param string $location Menu location identifier
 * @return bool
 */
if (!function_exists('has_nav_menu')) {
    function has_nav_menu($location) {
        return Menu::where('location', $location)
              ->where('status', 1)
              ->exists();
    }
} 