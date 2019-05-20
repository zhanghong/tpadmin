<?php

namespace tpadmin\validate;

use think\Validate;

class Config extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|min:2|max:30|unique:config',
    ];

    protected $field = [
        'name' => '配置名',
    ];
}
