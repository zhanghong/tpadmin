<?php

namespace tpadmin\service\auth;

use tpadmin\service\auth\contract\Authenticate;
use tpadmin\service\auth\guard\contract\Guard;
use tpadmin\exception\ValidateException;
use think\facade\Validate;
use think\Request;

class Auth implements contract\Auth
{
    public $failException = true;

    protected $authenticate;

    protected $adminer;

    protected $guard;

    public function __construct(Authenticate $authenticate, Guard $guard)
    {
        $this->authenticate = $authenticate;
        $this->guard = $guard;
    }

    public function login(Request $request)
    {
        $this->validate($request->param());

        if (!$this->attempt($request->param())) {
            $e = new ValidateException('数据验证失败');
            $e->setData(['admin_account' => '用户名或密码错误']);
            throw $e;
        }

        $this->guard()->login($this->adminer);
    }

    public function logout()
    {
        $this->guard()->logout();
    }

    public function user()
    {
        return $this->guard()->user();
    }

    public function guard()
    {
        return $this->guard;
    }

    protected function validate(array $data = [])
    {
        $validate = Validate::make([
            'admin_account' => 'require|max:25',
            'admin_password' => 'require|max:25',
            'captcha|验证码' => 'require|captcha',
        ], [
            'admin_account.require' => '登录名不能为空',
            'admin_account.max' => '登录名最多不能超过25个字符',
            'admin_password.require' => '登录密码不能为空',
            'admin_password.max' => '密码最多不能超过25个字符',
        ]);

        if (!$validate->batch(true)->check($data)) {
            if ($this->failException) {
                $e = new ValidateException('数据验证失败');
                $e->setData($validate->getError());
                throw $e;
            }
        }
    }

    public function attempt(array $credentials)
    {
        $adminer = $this->adminer = $this->authenticate->retrieveByCredentials([
            'name' => $credentials['admin_account'],
        ]);
        if (!$adminer) {
            return false;
        }

        return $this->validCredentials($adminer, $credentials);
    }

    protected function validCredentials($adminer, array $credentials)
    {
        return password_verify($credentials['admin_password'], $adminer->password);
    }
}
