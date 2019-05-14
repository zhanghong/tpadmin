<?php

namespace tpadmin\controller\auth;

use tpadmin\service\auth\contract\Auth;
use tpadmin\controller\Controller;
use think\Request;

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
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('登录成功', url('[tpadmin.index]'));
    }

    public function logout()
    {
        $this->auth->logout();
        return redirect('tpadmin.auth.passport.login');
    }
}
