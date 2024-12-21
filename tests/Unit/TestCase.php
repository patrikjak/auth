<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Unit;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Patrikjak\Auth\Tests\Traits\ConfigSetter;
use Patrikjak\Auth\Tests\Traits\TestingData;

class TestCase extends OrchestraTestCase
{
    use TestingData;
    use ConfigSetter;
}