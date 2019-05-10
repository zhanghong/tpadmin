<?php

namespace tpadmin\model;

use tpadmin\validate\AuthRule as Validate;
use think\exception\ValidateException;

class AuthRule extends Model
{
    use traits\Tree;

    public function pivotRoles()
    {
        return $this->hasMany('AuthRoleRule', 'rule_id');
    }

    public function roles()
    {
        return $this->belongsToMany('AuthRole', '\\tpadmin\\model\\AuthRoleRule', 'role_id', 'rule_id');
    }

    static public function createItem($data)
    {
        $validate = new Validate;
        return self::baesCreateItem($data, $validate);
    }

    static public function updateItem($id, $data)
    {
        $validate = new Validate;
        return self::baesUpdateItem($id, $data, $validate);
    }
}
