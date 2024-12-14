<?php

return [

    /**
     * Name of the application - used in the title
     */
    'app_name' => env('APP_NAME', 'App'),

    /**
     * Path to the logo - relative to the public directory
     * Set to null to disable the logo
     */
    'logo_path' => 'images/logo/logo.svg',

    /**
     * Path to the icon - relative to the public directory
     * Set to null to disable the icon
     */
    'icon' => [
        'path' => 'images/logo/favicon.svg',
        'type' => 'image/svg+xml',
    ],

    /**
     * Recaptcha configuration
     */
    'recaptcha' => [
        'enabled' => true,
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    /**
     * Social login configuration
     */
    'social_login' => [
        'google' => [
            'enabled' => true,
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT_URI'),
        ],
    ],

    /**
     * Features to enable or disable
     */
    'features' => [
        'register' => true,
        'login' => true,
        'password_reset' => true,
    ],

    /**
     * Models to use
     */
    'models' => [
        'user' => env('AUTH_MODEL', \Patrikjak\Auth\Models\User::class),
    ],

    /**
     * Repositories implementation to use
     */
    'repositories' => [
        'user' => \Patrikjak\Auth\Repositories\UserRepository::class,
    ],

    /**
     * Redirect to this path after login or register
     */
    'redirect_after_login' => env('REDIRECT_AFTER_LOGIN', '/dashboard'),

];