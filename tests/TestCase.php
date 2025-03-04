<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\Testing\Fakes\EventFake;
use Illuminate\Support\Testing\Fakes\NotificationFake;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Patrikjak\Auth\AuthServiceProvider;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Tests\Traits\ConfigSetter;
use Patrikjak\Auth\Tests\Traits\TestingData;
use Patrikjak\Auth\Tests\Traits\UserCreator;
use Patrikjak\Utils\Common\Http\Middlewares\VerifyRecaptcha;
use Patrikjak\Utils\UtilsServiceProvider;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends OrchestraTestCase
{
    use MatchesSnapshots {
        assertMatchesHtmlSnapshot as baseAssertMatchesHtmlSnapshot;
    }
    use ConfigSetter;
    use TestingData;
    use UserCreator;

    protected DatabaseManager $databaseManager;

    protected EventFake $eventFake;

    public function assertMatchesHtmlSnapshot(string $actual): void
    {
        $actual = preg_replace(
            '/<meta\s+name="csrf-token"\s+content="([^"]+)"/',
            '<meta name="csrf-token" content="{CSRF-TOKEN}"',
            $actual
        );

        $actual = preg_replace('/name="_token"\s+value="[^"]*"/', 'name="_token" value="{TOKEN}"', $actual);

        $actual = preg_replace(
            '/<input[^>]*name="token"\s*["\']?[^>]*value\s*=\s*["\']?([^"\'\s>]+)["\']?>/',
            '<input name="token" value="TOKEN"> <!-- REPLACED INPUT -->',
            $actual,
        );

        $this->baseAssertMatchesHtmlSnapshot($actual);
    }

    public function skipRecaptcha(): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->setLocale('test');
        $this->app->setFallbackLocale('test');

        $this->databaseManager = $this->app->make(DatabaseManager::class);
        $this->eventFake = $this->app->make(EventFake::class);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @param Application $app
     * @return array<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            AuthServiceProvider::class,
            UtilsServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @param Application $app
     */
    protected function defineEnvironment($app): void
    {
        tap($app['config'], static function (Repository $config): void {
            $config->set('auth.providers.users.model', User::class);
        });
    }
}