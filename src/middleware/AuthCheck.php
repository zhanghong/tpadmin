<?php

namespace tpadmin\middleware;

use tpadmin\service\auth\facade\Auth;

class AuthCheck
{
    public function handle($request, \Closure $next)
    {
        if (Auth::guard()->guest()) {
            return redirect('[tpadmin.auth.passport.login]');
        }

        return $next($request);
    }
}
