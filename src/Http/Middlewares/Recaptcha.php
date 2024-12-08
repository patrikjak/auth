<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class Recaptcha
{
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }
}
