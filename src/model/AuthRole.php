<?php

namespace tpadmin\model;

use tpadmin\validate\AuthRole as Validate;

class AuthRole extends Model
{
    public function pivotRules()
    {
        return $this->hasMany('AuthRoleRule', 'role_id');
    }

    public function rules()
    {
        return $this->belongsToMany('AuthRule', '\\tpadmin\\model\\AuthRoleRule', 'rule_id', 'role_id');
    }

    public function pivotUsers()
    {
        return $this->hasMany('AuthRoleUser', 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany('Adminer', '\\tpadmin\\model\\AuthRoleUser', 'user_id', 'role_id');
    }

    protected static function init()
    {
        self::afterDelete(function ($role) {
            AuthRoleRule::where('role_id', $role->id)->delete();
            AuthRoleUser::where('role_id', $role->id)->delete();
        });
    }

    static public function paginateSelect($params = [], $page_rows = 15)
    {
        $config = [];
        $search_fields = self::searchFields();
        $map = self::queryConditins($search_fields, $params);
        $config = ['query' => $map];
        $paginate = self::where($map)->order('id', 'ASC')->paginate($page_rows, false, $config);
        return $paginate;
    }

    static public function searchFields()
    {
        return [
            ['param_name' => 'keyword', 'column_name' => 'title', 'mode' => 'like'],
        ];
    }

    static public function createItem($data)
    {
        $validate = new Validate;
        $role = self::baesCreateItem($data, $validate);

        $rule_ids = [];
        if(isset($data['rule_ids'])){
            $rule_ids = $data['rule_ids'];
        }
        return $role->updateAllowRule($rule_ids);
    }

    static public function updateItem($id, $data)
    {
        $validate = new Validate;
        $role = self::baesUpdateItem($id, $data, $validate);

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

        if(!empty($new_rule_ids)){
            $this->rules()->saveAll($new_rule_ids);
        }

        if(empty($old_rule_ids)){
            $delete_rule_ids = [];
        }else if(empty($new_rule_ids)){
            $delete_rule_ids = $old_rule_ids;
        }else{
            $delete_rule_ids = array_diff($old_rule_ids, $new_rule_ids);
        }

        if(!empty($delete_rule_ids)){
            $this->rules()->detach($delete_rule_ids);
        }

        return $this;
    }

    public function allowRoleIds()
    {
        $rule_ids = AuthRoleRule::where('role_id', $this->id)->column('rule_id');
        return $rule_ids;
    }

    // static public function deleteItem($id)
    // {
    //     $id = intval($id);
    //     if($id < 1){
    //         return true;
    //     }

    //     $role = self::get($id, 'pivot_rules');
    //     if(empty($role)){
    //         return true;
    //     }

    //     return $role->runDelete();
    // }

    // public function runDelete()
    // {
    //     $this->together('pivot_rules')->delete();
    //     return true;
    // }
}
