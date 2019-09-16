<?php

namespace tpadmin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use tpadmin\model\Adminer;
use tpadmin\model\AuthRule;
use tpadmin\model\AuthRole;

class Seed extends Command
{
    protected function configure()
    {
        $this->setName('tpadmin:seed')->setDescription('seed tpadmin default data');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->createDefaultAdminer();
        $this->createMenus();
    }

    /**
     * 生成默认管理员账号
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   [type]             [description]
     */
    protected function createDefaultAdminer()
    {
        $adminers = [
            ['name' => 'admin', 'password' => '123456', 'status' => 1, 'is_default' => 1,],
            ['name' => 'manager', 'password' => '123456', 'status' => 1, 'is_default' => 0,],
        ];
        foreach ($adminers as $key => $data) {
            $data['password_confirm'] = $data['password'];
            $adminer = Adminer::where('name', $data['name'])->find();
            if(empty($adminer)){
                Adminer::createItem($data);
            }else{
                Adminer::updateItem($adminer->id, $data);
            }
        }
    }

    /**
     * 初始化后台菜单
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   [type]             [description]
     */
    protected function createMenus()
    {
        $auth_rules = [
            ['name' => 'index/index', 'title' => '控制台', 'route_name' => 'admin.index', 'icon' => 'fa fa-tachometer', 'parent_name' => ''],

            ['name' => 'setting/content', 'title' => '内容管理', 'route_name' => '', 'icon' => 'fa fa-laptop', 'parent_name' => ''],

            ['name' => 'setting/manager', 'title' => '系统设置', 'route_name' => '', 'icon' => 'fa fa-cogs', 'parent_name' => ''],
            ['name' => 'config/site', 'title' => '站点设置', 'route_name' => 'admin.config.site', 'icon' => '', 'parent_name' => 'setting/manager'],

            ['name' => 'auth/manager', 'title' => '安全管理', 'route_name' => '', 'icon' => 'fa fa-users', 'parent_name' => ''],

            ['name' => 'auth/adminer/index', 'title' => '管理员管理', 'route_name' => 'admin.auth.adminer.index', 'icon' => '', 'parent_name' => 'auth/manager'],
            ['name' => 'auth/adminer/create', 'title' => '添加管理员', 'route_name' => 'admin.auth.adminer.create', 'icon' => '', 'parent_name' => 'auth/adminer/index'],
            ['name' => 'auth/adminer/edit', 'title' => '编辑管理员', 'route_name' => 'admin.auth.adminer.edit', 'icon' => '', 'parent_name' => 'auth/adminer/index'],
            ['name' => 'auth/adminer/delete', 'title' => '删除管理员', 'route_name' => 'admin.auth.adminer.delete', 'icon' => '', 'parent_name' => 'auth/adminer/index'],

            ['name' => 'auth/role/index', 'title' => '角色组管理', 'route_name' => 'admin.auth.role.index', 'icon' => '', 'parent_name' => 'auth/manager'],
            ['name' => 'auth/role/create', 'title' => '添加角色组', 'route_name' => 'admin.auth.role.create', 'icon' => '', 'parent_name' => 'auth/role/index'],
            ['name' => 'auth/role/edit', 'title' => '编辑角色组', 'route_name' => 'admin.auth.role.edit', 'icon' => '', 'parent_name' => 'auth/role/index'],
            ['name' => 'auth/role/delete', 'title' => '删除角色组', 'route_name' => 'admin.auth.role.delete', 'icon' => '', 'parent_name' => 'auth/role/index'],

            ['name' => 'auth/rule/index', 'title' => '路由管理', 'route_name' => 'admin.auth.rule.index', 'icon' => '', 'parent_name' => 'auth/manager'],
            ['name' => 'auth/rule/create', 'title' => '添加路由', 'route_name' => 'admin.auth.rule.create', 'icon' => '', 'parent_name' => 'auth/rule/index'],
            ['name' => 'auth/rule/edit', 'title' => '编辑路由', 'route_name' => 'admin.auth.rule.edit', 'icon' => '', 'parent_name' => 'auth/rule/index'],
            ['name' => 'auth/rule/delete', 'title' => '删除路由', 'route_name' => 'admin.auth.rule.delete', 'icon' => '', 'parent_name' => 'auth/rule/index'],
        ];
        foreach ($auth_rules as $key => $data) {
            $parent_name = $data['parent_name'];
            unset($data['parent_name']);

            $parent_id = 0;
            if(!empty($parent_name)){
                $parent_rule = AuthRule::where('name', $parent_name)->find();
                if(!empty($parent_rule)){
                    $parent_id = $parent_rule->id;
                }
            }
            $data['parent_id'] = $parent_id;
            $rule = AuthRule::where('name', $data['name'])->find();
            if(empty($rule)){
                $rule = AuthRule::create($data);
            }else{
                AuthRule::where('id', $rule->id)->update($data);
            }
        }

        // 默认管理员群组
        $default_role_data = ['title' => '运营', 'status' => 1];
        $auth_role = AuthRole::where('title', $default_role_data['title'])->find();
        if(empty($auth_role)){
            $auth_role = AuthRole::create($default_role_data);
        }else{
            AuthRole::where('id', $auth_role->id)->update($default_role_data);
        }

        // $rule_ids = AuthRule::all()->column('id');
        // $auth_role->rules()->save($rule_ids);

        $manager = Adminer::where('name', 'manager')->find();
        $auth_role->users()->attach($manager->id);
    }
}
