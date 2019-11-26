<?php

namespace tpadmin\model;

use think\auth\model\Role as ThinkRole;
use tpadmin\validate\Role as Validate;
use tpadmin\exception\ValidateException;

class Role extends ThinkRole
{
    protected $name = "AuthRole";

    public function pivotRules()
    {
        return $this->hasMany('RoleRule', 'role_id');
    }

    public function pivotUsers()
    {
        return $this->hasMany('RoleUser', 'role_id');
    }

    public function role_users()
    {
        return $this->hasMany('RoleUser', 'role_id', 'id');
    }

    public static function paginateSelect($params = [], $page_rows = 15)
    {
        $map = [];
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            array_push($map, ['title', 'LIKE', '%'.$params['keyword'].'%']);
        }
        $config = ['query' => $map];
        $paginate = static::where($map)->order('id', 'ASC')->paginate($page_rows, false, $config);
        return $paginate;
    }

    public static function createItem($data)
    {
        $validate = new Validate;
        if (!empty($validate) && !$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $role->title = $data['title'];
        $role->status = $data['status'];
        $role->save();

        $rule_ids = [];
        if(isset($data['rule_ids'])){
            $rule_ids = $data['rule_ids'];
        }
        return $role->updateAllowRule($rule_ids);
    }

    public static function updateItem($id, $data)
    {
        $id = intval($id);
        $role = static::find($id);
        if(empty($role)){
            throw new \Exception('未找到更新记录');
        }

        $data['id'] = $id;
        $validate = new Validate;
        if (!$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $role->data($data, true)->save();

        $rule_ids = [];
        if(isset($data['rule_ids'])){
            $rule_ids = $data['rule_ids'];
        }
        return $role->updateAllowRule($rule_ids);
    }

    private function updateAllowRule($new_rule_ids)
    {
        if(!is_array($new_rule_ids)){
            $new_rule_ids = [];
        }
        $old_rule_ids = $this->allowRoleIds();

        foreach ($new_rule_ids as $key => $rule_id) {
            $rule_id = intval($rule_id);
            if ($rule_id < 1){
                continue;
            }
            $data = ['role_id' => $this->id, 'rule_id' => $rule_id];
            if (!RoleRule::where($data)->count()){
                RoleRule::create($data);
            }
        }

        if(empty($old_rule_ids)){
            $delete_rule_ids = [];
        }else if(empty($new_rule_ids)){
            $delete_rule_ids = $old_rule_ids;
        }else{
            $delete_rule_ids = array_diff($old_rule_ids, $new_rule_ids);
        }

        if(!empty($delete_rule_ids)){
            RoleRule::where('role_id', $this->id)->whereIn('rule_id', $delete_rule_ids)->delete();
        }

        return $this;
    }

    public function getStatusTextAttr()
    {
        if ($this->status) {
            return '启用';
        } else {
            return '禁用';
        }
    }

    public function allowRoleIds()
    {
        $rule_ids = RoleRule::where('role_id', $this->id)->column('rule_id');
        return $rule_ids;
    }
}
