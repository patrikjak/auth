<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Tests\Integration;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Patrikjak\Auth\AuthServiceProvider;
use Patrikjak\Auth\Tests\Traits\ConfigSetter;
use Patrikjak\Utils\UtilsServiceProvider;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends OrchestraTestCase
{
    use MatchesSnapshots {
        assertMatchesHtmlSnapshot as baseAssertMatchesHtmlSnapshot;
    }
    use ConfigSetter;

    protected function getPackageProviders($app): array
    {
        return [
            AuthServiceProvider::class,
            UtilsServiceProvider::class,
        ];
    }

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
}