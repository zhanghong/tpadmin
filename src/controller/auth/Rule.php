<?php

namespace tpadmin\controller\auth;

use think\Request;
use think\facade\Session;
use tpadmin\controller\Controller;
use tpadmin\exception\ValidateException;

use tpadmin\model\Rule as RuleModel;

class Rule extends Controller
{
    public function index()
    {
        $ruleModel = new RuleModel;
        $list = $ruleModel->flatTree();
        return $this->fetch('auth/rule/index', [
            'list' => $list,
        ]);
    }

    public function create(Request $request)
    {
        $ruleModel = new RuleModel;
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
            $rule = RuleModel::createItem($data);
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
        $rule = RuleModel::find($id);
        if(empty($rule)){
            return $this->redirect('[admin.auth.rule.index]');
        }

        $ruleModel = new RuleModel;
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
            $rule = RuleModel::updateItem($id, $data);
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
            RuleModel::destroy($id);
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
        RuleModel::resort($items);
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
            ['name' => 'status', 'type' => 'boolean', 'default' => 0],
        ];
        $data = $this->filterPostData($request, $filter_attrs);
        return $data;
    }
}
