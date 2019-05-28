<?php

namespace tpadmin\controller\auth;

use think\Request;
use think\facade\Session;
use tpadmin\controller\Controller;
use think\exception\ValidateException;

use tpadmin\model\AuthRule as AuthRuleModel;
use tpadmin\model\AuthRole as AuthRoleModel;

class Role extends Controller
{
    public function index(Request $request)
    {
        $param_fields = AuthRoleModel::searchFields();
        $params = $this->filterSearchData($request, $param_fields);
        $paginate = AuthRoleModel::paginateSelect($params);

        return $this->fetch('auth/role/index', [
            'paginate' => $paginate,
        ]);
    }

    public function create(Request $request)
    {
        $role = ['title' => '', 'status' => 1];
        $this->assign('role', $role);
        $this->assign('rule_ids', []);

        $ruleModel = new AuthRuleModel;
        $rule_tree = $ruleModel->toTree(AuthRuleModel::MENU_MODE_ALL);
        $this->assign('rule_tree', $rule_tree);

        return $this->fetch('auth/role/form');
    }

    public function save(Request $request)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $rule = AuthRoleModel::createItem($data);
        } catch (ValidateException $e) {
            $error_msg = $e->getError();
        } catch (\Exception $e) {
            $error_msg = $e->getError();
        }

        if(!is_null($error_msg)){
            return json(['msg' => $error_msg]);
            $this->error($error_msg);
        }

        $success_message = '创建成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.role.index]'));
    }

    public function edit(Request $request, $id)
    {
        $role = AuthRoleModel::find($id);
        if(empty($role)){
            $this->redirect('[admin.auth.role.index]');
        }
        $this->assign('role', $role);
        $this->assign('rule_ids', $role->allowRoleIds());

        $ruleModel = new AuthRuleModel;
        $rule_tree = $ruleModel->toTree(AuthRuleModel::MENU_MODE_ALL);
        $this->assign('rule_tree', $rule_tree);

        return $this->fetch('auth/role/form');
    }

    public function update(Request $request, $id)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $role = AuthRoleModel::updateItem($id, $data);
        } catch (ValidateException $e) {
            $error_msg = $e->getError();
        } catch (\Exception $e) {
            $error_msg = $e->getError();
        }

        if(!is_null($error_msg)){
            $this->error($error_msg);
        }

        $success_message = '更新成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.role.index]'));
    }

    public function read(Request $request, $id)
    {
        Session::flash('info', '您访问的页面不存在');
        $this->redirect('[admin.auth.role.index]');
    }

    public function delete(Request $request, $id)
    {
        $error_msg = NULL;

        try {
            AuthRoleModel::destroy($id);
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
        }

        if(!is_null($error_msg)){
            $this->error($error_msg);
        }

        $success_message = '删除成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.role.index]'));
    }

    private function getPostData($request)
    {
        $filter_attrs = [
            ['name' => 'title', 'type' => 'string', 'default' => ''],
            ['name' => 'status', 'type' => 'boolean', 'default' => 1],
            ['name' => 'rule_ids', 'type' => 'array', 'default' => []],
        ];
        $data = $this->filterPostData($request, $filter_attrs);
        return $data;
    }
}