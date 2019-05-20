<?php

namespace tpadmin\model;

use tpadmin\service\auth\facade\Auth;
use tpadmin\validate\AdminerCreate as ValidateCreate;
use tpadmin\validate\AdminerUpdate as ValidateUpdate;
use tpadmin\service\auth\contract\Authenticate;

class Adminer extends Model implements Authenticate
{
    public function pivotRoles()
    {
        return $this->hasMany('AuthRoleUser', 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany('AuthRole', '\\tpadmin\\model\\AuthRoleUser', 'role_id', 'user_id');
    }

    protected static function init()
    {
        self::afterDelete(function ($adminer) {
            AuthRoleUser::where('user_id', $adminer->id)->delete();
        });
    }

    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->db();
        foreach ($credentials as $key => $value) {
            $query->where($key, $value);
        }

        return $query->find();
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->pk};
    }

    public function retrieveByIdentifier($identifier)
    {
        return $this->find($identifier);
    }

    public static function paginateSelect($params = [], $page_rows = 15)
    {
        $config = [];
        $map = self::queryConditins($params);
        $config = ['query' => $map];
        $paginate = self::with('roles')->where($map)->order('id', 'ASC')->paginate($page_rows, false, $config);
        return $paginate;
    }

    public static function searchFields()
    {
        return [
            ['param_name' => 'keyword', 'column_name' => 'name', 'mode' => 'like'],
        ];
    }

    public static function createItem($data)
    {
        $validate = new ValidateCreate;
        $adminer = self::baesCreateItem($data, $validate);

        $role_id = NULL;
        if(isset($data['role_id'])){
            $role_id = intval($data['role_id']);
        }
        return $adminer->updateRole($role_id);
    }

    public static function updateItem($id, $data)
    {
        if(empty($data['password'])){
            unset($data['password']);
            unset($data['password_confirm']);
        }else if(empty($data['password_confirm'])){
            $data['password_confirm'] = '1';
        }

        $validate = new ValidateUpdate;
        $adminer = self::baesUpdateItem($id, $data, $validate);

        $role_id = NULL;
        if(isset($data['role_id'])){
            $role_id = intval($data['role_id']);
        }
        return $adminer->updateRole($role_id);
    }

    private function updateRole($role_id)
    {
        if($this->is_default){
            return true;
        }else if(is_null($role_id)){
            return true;
        }else if($role_id == 0){
            AuthRoleUser::where('user_id', $this->id)->delete();
            return true;
        }

        $role_user = AuthRoleUser::where('user_id', $this->id)->find();
        if(empty($role_user)){
            AuthRoleUser::create(['user_id' => $this->id, 'role_id' => $role_id]);
        }else if($role_user->role_id != $role_id){
            $role_user->role_id = $role_id;
            $role_user->save();
        }
        return true;
    }

    public static function deleteItem($id)
    {
        $current_adminer = Auth::user();
        if(empty($current_adminer)){
            throw new \Exception('请先登录系统');
        }else if($id == $current_adminer->id){
            throw new \Exception('自己不能删除自己账号');
        }

        $adminer = self::find($id);
        if(empty($adminer)){
            return true;
        }

        return $adminer->runDelete();
    }

    public function runDelete()
    {
        if($this->is_default){
            throw new \Exception('不能删除默认管理员');
        }
        return $this->delete();
    }

    public function getRoleTitlesAttr()
    {
        $titles = [];
        foreach ($this->roles as $key => $role) {
            array_push($titles, $role->title);
        }
        return implode(',', $titles);
    }
}
