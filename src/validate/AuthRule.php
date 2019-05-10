<?php

namespace tpadmin\validate;

use think\Validate;

class AuthRule extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require|min:2|max:10',
        'name' => 'require|min:1|max:50',
        'route_name' => 'max:50',
        'icon' => 'max:50',
        'sort_num' => 'egt:0|elt:9999',
    ];

    protected $field = [
        'title' => '菜单标题',
        'name' => '页面名称',
        'route_name' => '路由名称',
        'icon' => 'ICON图标',
        'sort_num' => '排序编号',
    ];
}
