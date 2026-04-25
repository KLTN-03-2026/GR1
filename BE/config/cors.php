<?php

$frontendOrigin = env('FRONTEND_URL', 'http://localhost:5173');

return [
    'paths' => ['api/*', 'broadcasting/auth', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [$frontendOrigin],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'Origin'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
