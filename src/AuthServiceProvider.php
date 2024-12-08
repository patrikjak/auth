<?php

namespace Patrikjak\Auth;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerComponents();

        $this->loadRoutes();
        $this->loadTranslations();
        $this->loadViews();

        $this->publishAssets();
        $this->publishConfig();
        $this->publishViews();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pjauth.php', 'pjauth');
    }

    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/pjauth'),
        ], 'assets');

        $this->publishes(
            [__DIR__ . '/../resources/images' => public_path('vendor/pjauth/assets/images')],
            ['assets', 'images'],
        );
    }

    private function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/pjauth.php' => config_path('pjauth.php'),
        ], 'config');
    }

    private function publishViews(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/pjauth'),
        ], 'views');
    }

    private function registerComponents(): void
    {
        Blade::componentNamespace('Patrikjak\\Auth\\View', 'pjauth');
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pjauth');
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'pjauth');
    }
}