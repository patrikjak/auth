<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Tests\Mocks\Models;

use Patrikjak\Utils\Common\Traits\EnumValues;

enum CustomRoles: int
{
    use EnumValues;

    case ROLE = 1;
}
