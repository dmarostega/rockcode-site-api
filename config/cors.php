<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | The analytics endpoint is consumed by the public Rock Code Labs frontend.
    | Keep the origin list explicit so browser integrations work without
    | exposing unrelated API routes to arbitrary origins.
    |
    */

    'paths' => ['api/analytics/events'],

    'allowed_methods' => ['POST', 'OPTIONS'],

    'allowed_origins' => array_filter(array_map('trim', explode(',', env(
        'CORS_ALLOWED_ORIGINS',
        'https://rockcodelabs.com.br,https://www.rockcodelabs.com.br'
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept', 'Origin', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 600,

    'supports_credentials' => false,

];
