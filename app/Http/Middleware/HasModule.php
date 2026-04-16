<?php

namespace App\Http\Middleware;

use App\Enums\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasModule
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $license = $request->user()?->license;

        if (!$license || !$license->isValid() || !$license->hasModule(Module::from($module))) {
            abort(403, 'This feature requires an active license for the ' . $module . ' module.');
        }

        return $next($request);
    }
}
