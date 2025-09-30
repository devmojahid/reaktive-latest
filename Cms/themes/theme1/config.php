<?php

return [
    'inherit' => null,
    
    'events' => [
        'beforeRenderTheme' => function ($theme) {
            // Add theme assets
            $theme->asset()->usePath()->add('theme-style', 'css/theme.css');
            $theme->asset()->container('footer')->usePath()->add('theme-script', 'js/theme.js');
        },
    ],
]; 