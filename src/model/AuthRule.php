<?php

namespace tpadmin\model;

use tpadmin\validate\AuthRule as Validate;

class AuthRule extends Model
{
    const MENU_MODE_ALL = 'all_menu';
    const MENU_MODE_USER = 'user_menu';

    use traits\Tree;

    public function pivotRoles()
    {
        return $this->hasMany('AuthRoleRule', 'rule_id');
    }

    public function roles()
    {
        return $this->belongsToMany('AuthRole', '\\tpadmin\\model\\AuthRoleRule', 'role_id', 'rule_id');
    }

    protected static function init()
    {
        self::afterDelete(function ($rule) {
            AuthRoleRule::where('rule_id', $rule->id)->delete();
        });
    }

    public static function createItem($data)
    {
        $validate = new Validate;
        return self::baesCreateItem($data, $validate);
    }

    public static function updateItem($id, $data)
    {
        $validate = new Validate;
        return self::baesUpdateItem($id, $data, $validate);
    }
}
