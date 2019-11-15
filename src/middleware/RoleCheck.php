<?php

namespace tpadmin\middleware;

use think\Auth;
use think\helper\Str;
use think\facade\Session;
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

        $ctr_name = $request->controller(true);
        $act_name = $request->action(true);
        if (empty($ctr_name) || empty($act_name)) {
            $router = $request->rule()->getRoute();
            $route_name = str_replace(['\\', '@'], '/', Str::snake($router));
        } else {
            $route_name = $ctr_name.'/'.$act_name;
        }
        $route_name = str_replace('save', 'create', $route_name);
        $route_name = str_replace('update', 'edit', $route_name);

        if(!auth_check($route_name, $adminer->id)){
            $url = (string) url('[admin.index]');
            $error_msg = '对不起，您没有权限访问该页面';
            if($request->isAjax()){
                $result = [
                    'code' => 0,
                    'msg'  => $error_msg,
                    'data' => [],
                    'url'  => $url,
                ];
                return json($result);
            }else{
                Session::flash('danger', $error_msg);
                return redirect($url);
            }
        }

        return $next($request);
    }
}