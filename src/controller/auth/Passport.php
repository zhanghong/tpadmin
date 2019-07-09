<?php

namespace tpadmin\controller\auth;

use tpadmin\service\auth\contract\Auth;
use tpadmin\controller\Controller;
use think\Request;
use think\facade\Route;
use tpadmin\exception\ValidateException;

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
        return $this->fetch('auth/passport/login');
    }

    public function loginAuth(Request $request)
    {
        try {
            $this->auth->login($request);
        }catch (ValidateException $e){
            $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
        $this->success('登录成功', url('[admin.index]'));
    }

    public function logout()
    {
        $this->auth->logout();
        return redirect('[admin.auth.passport.login]');
    }
}
