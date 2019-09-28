<?php

namespace tpadmin\middleware;

use think\facade\Session;
use think\Auth;
use think\facade\Config;
use tpadmin\service\auth\facade\Auth as AuthFacade;
use tpadmin\model\AuthRule as AuthRuleModel;

class RoleCheck
{
    public function handle($request, \Closure $next)
    {
        $adminer = AuthFacade::user();
        if($adminer->is_default){
            return $next($request);
        }

        $route_info = request()->routeInfo();
        if(empty($route_info) || empty($route_info['route'])){
            $error_msg = '对不起，您访问的页面不存在';
            if($request->isAjax()){
                $result = [
                    'code' => 0,
                    'msg'  => $error_msg,
                    'data' => [],
                    'url'  => url('[admin.index]'),
                ];
                return json($result);
            }else{
                Session::flash('danger', $error_msg);
                return redirect('[admin.index]');
            }
        }

        $route_name = strtolower(str_replace('@', '/', $route_info['route']));
        $route_name = str_replace('save', 'create', $route_name);
        $route_name = str_replace('update', 'edit', $route_name);
        if(!auth_check($route_name, $adminer->id)){
            $error_msg = '对不起，您没有权限访问该页面';
            if($request->isAjax()){
                $result = [
                    'code' => 0,
                    'msg'  => $error_msg,
                    'data' => [],
                    'url'  => url('[admin.index]'),
                ];
                return json($result);
            }else{
                Session::flash('danger', $error_msg);
                return redirect('[admin.index]');
            }
        }

        return $next($request);
    }
}
