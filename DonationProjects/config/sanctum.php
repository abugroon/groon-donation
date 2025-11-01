<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:8000,localhost:8080',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'guard' => ['web'],

    'expiration' => null,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
        'add_cookies_to_response' => Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        'create_fresh_api_token' => Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ],
];
