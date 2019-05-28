<?php

namespace tpadmin\controller\auth;

use tpadmin\service\auth\contract\Auth;
use tpadmin\controller\Controller;
use think\Request;
use think\facade\Route;

class Passport extends Controller
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        parent::__construct();
        $this->auth = $auth;
    }

    public function user()
    {
        try {
            $adminer = $this->auth->user();
            return json(
                [
                    'name' => $adminer->name,
                    'login_time' => $adminer->login_time,
                ]
            );
        } catch (\Exception $e) {
            return json([]);
        }
    }

    public function login()
    {
        // $routePath = admin_route_path();
        // $files = scandir($routePath);
        // foreach ($files as $file) {
        //     if (strpos($file, '.php')) {
        //         $filename = $routePath.$file;
        //         $rules = include_once $filename;
        //         var_dump($rules);
        //         // 导入路由配置
        //         // Route::group('admin', function () use ($filename) {
        //         //     $rules = include_once $filename;
        //         //     var_dump($rules);
        //         //     // if (\is_array($rules)) {
        //         //     //     $this->app->route->import($rules);
        //         //     // }
        //         // })->prefix('admin');
        //     }
        // }
        // exit();
        return $this->fetch('auth/passport/login');
    }

    public function loginAuth(Request $request)
    {
        try {
            $this->auth->login($request);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('登录成功', url('[admin.index]'));
    }

    public function logout()
    {
        $this->auth->logout();
        return redirect('[tpadmin.auth.passport.login]');
    }
}
