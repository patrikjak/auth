<?php

declare(strict_types = 1);

namespace Patrikjak\Auth;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Patrikjak\Auth\Console\Commands\CreateUsersCommand;
use Patrikjak\Auth\Console\Commands\SeedUserRoles;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository as RoleRepositoryInterface;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository as UserRepositoryInterface;
use Patrikjak\Auth\Repositories\RoleRepository;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerComponents();

        $this->loadRoutes();
        $this->loadTranslations();
        $this->loadViews();
        $this->loadCommands();

        $this->publishAssets();
        $this->publishConfig();
        $this->publishViews();
        $this->publishMigrations();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pjauth.php', 'pjauth');

        $this->registerServices();
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

    private function publishMigrations(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');
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
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'pjauth');
    }

    private function loadCommands(): void
    {
        $this->commands([
            SeedUserRoles::class,
            CreateUsersCommand::class,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    private function registerServices(): void
    {
        $config = $this->app->make(Repository::class);
        
        $this->app->bind(UserRepositoryInterface::class, $config->get('pjauth.repositories.user'));
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }
}