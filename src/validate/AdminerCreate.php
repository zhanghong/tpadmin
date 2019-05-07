<?php

namespace tpadmin\validate;

use think\Validate;

class AdminerCreate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|unique:adminer|length:3,20',
        'password' => 'require|length:6,20',
        'password_confirm' => 'require|length:6,20|confirm:password',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '用户名不能为空',
        'name.unique' => '用户名已存在',
        'name.length' => '用户名长度必须在3-20个字符之间',
        'password.require' => '登录密码不能为空',
        'password.length' => '登录密码长度必须在6-20之间',
        'password_confirm.require' => '重复密码不能为空',
        'password_confirm.length' => '重复密码长度必须在6-20之间',
        'password_confirm.confirm' => '两次输入的密码不一致',
    ];

    protected $scene = [
        'create'  =>  ['name', 'password', 'password_confirmation'],
    ];
}
