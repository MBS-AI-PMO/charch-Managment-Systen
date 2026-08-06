<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user() ?? auth('web')->user();

        if (! $user) {
            return $next($request);
        }

        if (! ($user->password_change_required ?? false)) {
            return $next($request);
        }

        $allowed = [
            'admin.password.force',
            'admin.password.force.update',
            'admin.logout',
            'logout',
            'password.force',
            'password.force.update',
        ];

        $name = $request->route()?->getName();
        if (in_array($name, $allowed, true)) {
            return $next($request);
        }

        return redirect()->route(auth('admin')->check() ? 'admin.password.force' : 'password.force');
    }
}
