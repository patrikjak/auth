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
        ],
    ],

    /**
     * Features to enable or disable
     */
    'features' => [
        'register' => true,
        'login' => true,
        'password_reset' => true,
        'change_password' => true,
        'register_via_invitation' => false,
    ],

    /**
     * Repositories implementation to use
     */
    'repositories' => [
        'user' => \Patrikjak\Auth\Repositories\UserRepository::class,
    ],

    /**
     * Models to use
     */
    'models' => [
        'role' => \Patrikjak\Auth\Models\Role::class,
    ],

    /**
     * Redirect to this path after login or register
     */
    'redirect_after_login' => env('REDIRECT_AFTER_LOGIN', '/dashboard'),

    /**
     * Redirect to this path after logout
     */
    'redirect_after_logout' => env('REDIRECT_AFTER_LOGOUT', '/'),

    /**
     * Default password for the user
     */
    'user_default_password' => env('USER_DEFAULT_PASSWORD', 'pass'),

];