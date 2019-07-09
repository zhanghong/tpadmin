<?php

namespace tpadmin\middleware;

use tpadmin\service\auth\facade\Auth;

class AuthCheck
{
    public function handle($request, \Closure $next)
    {
        if (Auth::guard()->guest()) {
            if($request->isAjax()){
                $result = [
                    'code' => 0,
                    'msg'  => '请先登录系统',
                    'data' => [],
                    'url'  => url('[admin.auth.passport.login]'),
                ];
                return json($result);
            }else{
                return redirect('[admin.auth.passport.login]');
            }
        }

        return $next($request);
    }
}
