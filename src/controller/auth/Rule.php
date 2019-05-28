<?php

namespace tpadmin\controller\auth;

use think\Request;
use think\facade\Session;
use tpadmin\controller\Controller;
use think\exception\ValidateException;

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
        $rule = ['parent_id' => 0];
        $this->assign('rule', $rule);

        $ruleModel = new AuthRuleModel;
        $parent_rules = $ruleModel->flatTree();
        $this->assign('parent_rules', $parent_rules);

        return $this->fetch('auth/rule/form');
    }

    public function save(Request $request)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $rule = AuthRuleModel::createItem($data);
        } catch (ValidateException $e) {
            $error_msg = $e->getError();
        } catch (\Exception $e) {
            $error_msg = $e->getError();
        }

        if(!is_null($error_msg)){
            $this->error($error_msg);
        }

        $success_message = '创建成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.rule.index]'));
    }

    public function edit(Request $request, $id)
    {
        $rule = AuthRuleModel::find($id);
        if(empty($rule)){
            $this->redirect('[admin.auth.rule.index]');
        }
        $this->assign('rule', $rule);

        $ruleModel = new AuthRuleModel;
        $parent_rules = $ruleModel->flatTree();
        $this->assign('parent_rules', $parent_rules);

        return $this->fetch('auth/rule/form');
    }

    public function update(Request $request, $id)
    {
        $error_msg = NULL;

        $data = $this->getPostData($request);

        try{
            $rule = AuthRuleModel::updateItem($id, $data);
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
        return $this->success($success_message, url('[admin.auth.rule.index]'));
    }

    public function read(Request $request, $id)
    {
        Session::flash('info', '您访问的页面不存在');
        $this->redirect('[admin.auth.rule.index]');
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
            $this->error($error_msg);
        }

        $success_message = '删除成功';
        Session::flash('success', $success_message);
        return $this->success($success_message, url('[admin.auth.rule.index]'));
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