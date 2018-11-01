<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true,

        // View settings
        'view' => [
            'template_path' => __DIR__ . '/templates',
            'twig' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
                'auto_reload' => true,
                'paths' => [
                    'public' => __DIR__ . '/../public'
                ],
            ],
        ],

        // Twig Assets settings
        'assets' => [
            // Public assets cache directory
            'path' => __DIR__ . '/../public/cache',
            // Cache settings
            'cache_enabled' => false,
            'cache_path' => __DIR__ . '/../cache',
            'cache_name' => 'assets',
            'cache_lifetime' => 0,
            // Enable JavaScript and CSS compression
            'minify' => 0
        ],

        // monolog settings
        'logger' => [
            'name' => 'ungheni',
            'path' => __DIR__ . '/../log/app.log',
        ],

        // Deployd settings
        'deployd' => [
            'host' => 'api.ungheni.today',
            'protocol' => 'https',
        ],
    ],
];
