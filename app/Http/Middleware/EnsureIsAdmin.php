<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->guest(route('admin.login'));
        }

        if (! Auth::guard('admin')->user()->is_admin) {
            Auth::guard('admin')->logout();

            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
