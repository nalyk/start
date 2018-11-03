<?php
return  [
        'template_path' => __DIR__ . '/templates',
        'twig' => [
            'cache' => __DIR__ . '/../../../cache/twig',
            'debug' => true,
            'auto_reload' => true,
            'paths' => [
                'public' => __DIR__ . '/public'
            ],
        ],
    ]
;