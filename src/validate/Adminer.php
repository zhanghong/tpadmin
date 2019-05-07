<?php

namespace tpadmin\validate;

use think\Validate;

class Adminer extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => 'require|gt:0',
        'name' => 'require|length:3,20',
        'password' => 'require|length:6,20',
        'password_confirmation' => 'require|length:6,20|confirm:password',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '用户名不能为空',
        'name.length' => '用户名长度必须在2-20个字符之间',
        'password.require' => '登录密码不能为空',
        'password.length' => '登录密码长度必须在6-20之间',
        'password_confirmation.require' => '重复密码不能为空',
        'password_confirmation.length' => '重复密码长度必须在6-20之间',
        'password_confirmation.confirm' => '两次输入的密码不一致',
    ];

    protected $scene = [
        'create'  =>  ['name', 'password', 'password_confirmation'],
    ];
}
