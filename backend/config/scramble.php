<?php

return [
    'info' => [
        'title' => 'NexaDoc API',
        'description' => 'API for NexaDoc digital library management system',
        'version' => '1.0.0',
    ],

    'servers' => [
        [
            'url' => env('APP_URL', 'http://localhost'),
            'description' => 'Main server',
        ],
    ],

    'ui' => [
        'api_documentation_path' => '/docs/api',
    ],

    'tags' => [
        'Auth',
        'Users',
        'Categories',
        'Authors',
        'Publishers',
        'Keywords',
        'References',
        'Deposit Requests',
        'Dashboard',
        'Statistics',
        'Notifications',
    ],
];
