<?php

namespace tpadmin\controller\auth;

use think\App;
use think\facade\Route;
use think\facade\Config;
use tpadmin\controller\Controller;
use tpadmin\service\auth\contract\Auth;
use tpadmin\exception\ValidateException;

class Passport extends Controller
{
    protected $auth;

    public function __construct(App $app, Auth $auth)
    {
        parent::__construct($app);
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

    public function create()
    {
        return $this->fetch('auth/passport/create');
    }

    public function save()
    {
        try {
            $this->auth->login($this->request);
        }catch (ValidateException $e){
            return $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
        return $this->success('登录成功', url('[admin.index]'));
    }

    public function delete()
    {
        $this->auth->logout();
        return $this->redirect('[admin.auth.passport.login]');
    }
}