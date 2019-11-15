<?php

namespace tpadmin\controller\auth;

use think\Request;
use think\facade\Session;
use tpadmin\controller\Controller;
use tpadmin\exception\ValidateException;

use tpadmin\model\AuthRule as AuthRuleModel;

class Rule extends Controller
{
    public function index()
    {
        $ruleModel = new AuthRuleModel;
        $list = $ruleModel->flatTree();
        return $this->fetch('auth/rule/index', [
            'list' => $list,
        ]);
    }

    public function create(Request $request)
    {
        $ruleModel = new AuthRuleModel;
        $parent_rules = $ruleModel->flatTree();

        return $this->fetch('auth/rule/form', [
            'rule' => ['parent_id' => 0],
            'parent_rules' => $ruleModel->flatTree(),
        ]);
    }

    public function save(Request $request)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $rule = AuthRuleModel::createItem($data);
        }catch (ValidateException $e){
            return $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

        $success_message = '创建成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.rule.index]'));
    }

    public function edit(Request $request, $id)
    {
        $rule = AuthRuleModel::find($id);
        if(empty($rule)){
            return $this->redirect('[admin.auth.rule.index]');
        }

        $ruleModel = new AuthRuleModel;
        $parent_rules = $ruleModel->flatTree();

        return $this->fetch('auth/rule/form', [
            'rule' => $rule,
            'parent_rules' => $ruleModel->flatTree(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $rule = AuthRuleModel::updateItem($id, $data);
        }catch (ValidateException $e){
            return $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

        $success_message = '更新成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.rule.index]'));
    }

    public function read(Request $request, $id)
    {
        Session::flash('info', '您访问的页面不存在');
        return $this->redirect('[admin.auth.rule.index]');
    }

    public function delete(Request $request, $id)
    {
        $error_msg = NULL;

        try {
            AuthRuleModel::destroy($id);
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
        }

        if(!is_null($error_msg)){
            return $this->error($error_msg);
        }

        $success_message = '删除成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.rule.index]'));
    }

    public function resort(Request $request)
    {
        $items = $request->post('items');
        AuthRuleModel::resort($items);
        return $this->redirect('[admin.auth.rule.index]');
    }

    private function getPostData($request)
    {
        $filter_attrs = [
            ['name' => 'name', 'type' => 'string', 'default' => ''],
            ['name' => 'title', 'type' => 'string', 'default' => ''],
            ['name' => 'parent_id', 'type' => 'integer', 'default' => 0],
            ['name' => 'sort_num', 'type' => 'integer', 'default' => 0],
            ['name' => 'route_name', 'type' => 'string', 'default' => ''],
            ['name' => 'icon', 'type' => 'string', 'default' => ''],
            ['name' => 'condition', 'type' => 'string', 'default' => ''],
            ['name' => 'tips', 'type' => 'string', 'default' => ''],
        ];
        $data = $this->filterPostData($request, $filter_attrs);
        return $data;
    }
}