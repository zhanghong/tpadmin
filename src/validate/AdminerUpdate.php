<?php

namespace tpadmin\validate;

use think\Validate;

class AdminerUpdate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|unique:adminer|length:3,20',
        'password' => 'length:6,20',
        'password_confirm' => 'length:6,20|confirm:password',
    ];

    protected $field = [
        'name' => '用户名',
        'password' => '登录密码',
        'password_confirm' => '确认密码',
    ];
}
