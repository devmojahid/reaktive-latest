<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The default theme to use if no theme is specified.
    |
    */
    'default' => 'theme1',

    /*
    |--------------------------------------------------------------------------
    | Theme Cache
    |--------------------------------------------------------------------------
    |
    | Whether to cache theme information.
    |
    */
    'cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Theme Assets Path
    |--------------------------------------------------------------------------
    |
    | The path where theme assets are stored.
    |
    */
    'assets_path' => 'cms/themes',

    /*
    |--------------------------------------------------------------------------
    | Available Themes
    |--------------------------------------------------------------------------
    |
    | List of available themes.
    |
    */
    'themes' => [
        'theme1' => [
            'name' => 'Theme One',
            'description' => 'The default light theme for Trips platform',
        ],
        'theme2' => [
            'name' => 'Theme Two',
            'description' => 'The dark theme for Trips platform',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Switcher
    |--------------------------------------------------------------------------
    |
    | Enable or disable theme switching functionality.
    |
    */
    'enable_switching' => true,
];