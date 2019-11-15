<?php

namespace tpadmin\validate;

use think\Validate;

class AuthRole extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require|min:2|max:10|unique:auth_role',
    ];

    protected $field = [
        'title' => '角色组名',
    ];
}