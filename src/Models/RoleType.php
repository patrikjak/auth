<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Patrikjak\Utils\Common\Traits\EnumValues;

enum RoleType: int
{
    use EnumValues;

    case SUPERADMIN = 1;
    case ADMIN = 2;
    case USER = 3;
}
