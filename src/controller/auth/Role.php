<?php

namespace tpadmin\controller\auth;

use think\Request;
use think\facade\Session;
use tpadmin\controller\Controller;
use tpadmin\exception\ValidateException;

use tpadmin\model\Rule as RuleModel;
use tpadmin\model\Role as RoleModel;

class Role extends Controller
{
    public function index(Request $request)
    {
        $param_fields = [
            ['param_name' => 'keyword', 'column_name' => 'title', 'mode' => 'like'],
        ];
        $params = $this->filterSearchData($request, $param_fields);
        $paginate = RoleModel::paginateSelect($params);

        return $this->fetch('auth/role/index', [
            'paginate' => $paginate,
        ]);
    }

    public function create(Request $request)
    {
        $ruleModel = new RuleModel;
        $rule_tree = $ruleModel->toTree(RuleModel::MENU_MODE_ALL);

        return $this->fetch('auth/role/form', [
            'rule_ids' => [],
            'role' => ['title' => '', 'status' => 1],
            'rule_tree' => $rule_tree,
        ]);
    }

    public function save(Request $request)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $rule = RoleModel::createItem($data);
        }catch (ValidateException $e){
            return $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

        $success_message = '创建成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.role.index]'));
    }

    public function edit(Request $request, $id)
    {
        $role = RoleModel::find($id);
        if(empty($role)){
            return $this->redirect('[admin.auth.role.index]');
        }

        $ruleModel = new RuleModel;
        $rule_tree = $ruleModel->toTree(RuleModel::MENU_MODE_ALL);

        return $this->fetch('auth/role/form', [
            'role' => $role,
            'rule_ids' => $role->allowRoleIds(),
            'rule_tree' => $rule_tree,
        ]);
    }

    public function update(Request $request, $id)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $role = RoleModel::updateItem($id, $data);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $success_message = '更新成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.role.index]'));
    }

    public function read(Request $request, $id)
    {
        Session::flash('info', '您访问的页面不存在');
        return $this->redirect('[admin.auth.role.index]');
    }

    public function delete(Request $request, $id)
    {
        $error_msg = NULL;

        try {
            RoleModel::destroy($id);
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
        }

        if(!is_null($error_msg)){
            return $this->error($error_msg);
        }

        $success_message = '删除成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.role.index]'));
    }

    private function getPostData($request)
    {
        $filter_attrs = [
            ['name' => 'title', 'type' => 'string', 'default' => ''],
            ['name' => 'status', 'type' => 'boolean', 'default' => '0'],
            ['name' => 'rule_ids', 'type' => 'array', 'default' => []],
        ];
        $data = $this->filterPostData($request, $filter_attrs);
        return $data;
    }
}
