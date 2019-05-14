<?php

namespace tpadmin\controller\auth;

use think\Request;
use tpadmin\controller\Controller;
use think\exception\ValidateException;

use tpadmin\model\Adminer as AdminerModel;
use tpadmin\model\AuthRole as AuthRoleModel;
use tpadmin\model\AuthRoleUser as AuthRoleUserModel;

class Adminer extends Controller
{
    public function index(Request $request)
    {
        $param_fields = AdminerModel::searchFields();
        $params = $this->filterSearchData($request, $param_fields);
        $paginate = AdminerModel::paginateSelect($params);
        return $this->fetch('auth/adminer/index', [
            'paginate' => $paginate,
        ]);
    }

    public function create(Request $request)
    {
        $adminer = [];
        $roles = AuthRoleModel::all();
        $role_id = 0;

        return $this->fetch('auth/adminer/create', compact('adminer', 'role_id', 'roles'));
    }

    public function save(Request $request)
    {
        $data = $this->getPostData($request);
        try {
            $adminer = AdminerModel::createItem($data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->success('创建成功', url('[tpadmin.auth.adminer.index]'));
    }

    public function edit(Request $request, $id)
    {
        $adminer = AdminerModel::find(intval($id));
        if(empty($adminer)){
            $this->error('未找到被管理员信息');
        }

        $roles = AuthRoleModel::all();
        $role_user = AuthRoleUserModel::where('user_id', $adminer->id)->find();
        if(empty($role_user)){
            $role_id = 0;
        }else{
            $role_id = $role_user->role_id;
        }

        return $this->fetch('auth/adminer/edit', compact('adminer', 'role_id', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->getPostData($request);
        try {
            $adminer = AdminerModel::updateItem($id, $data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->success('更新成功', url('[tpadmin.auth.adminer.index]'));
    }

    public function delete(Request $request, $id)
    {
        $id = intval($id);
        try {
            AdminerModel::deleteItem($id);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success('删除成功');
    }

    public function read(Request $request, $id)
    {
        $this->redirect('[tpadmin.auth.adminer.index]');
    }

    private function getPostData($request)
    {
        $filter_attrs = [
            ['name' => 'name', 'type' => 'string', 'default' => ''],
            ['name' => 'password', 'type' => 'string', 'default' => ''],
            ['name' => 'password_confirm', 'type' => 'string', 'default' => ''],
            ['name' => 'role_id', 'type' => 'integer', 'default' => 0],
        ];
        $data = $this->filterPostData($request, $filter_attrs);
        return $data;
    }
}