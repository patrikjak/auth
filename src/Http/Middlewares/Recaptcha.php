<?php

namespace Patrikjak\Auth\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class Recaptcha
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
