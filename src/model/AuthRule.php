<?php

namespace tpadmin\model;

use tpadmin\service\auth\facade\Auth;
use tpadmin\validate\AuthRule as Validate;

class AuthRule extends Model
{
    const MENU_MODE_ALL = 'all_menu';
    const MENU_MODE_USER = 'user_menu';

    const USER_ALLOWS = 'user_rules';

    protected $append = ['status_text'];

    use traits\Tree;

    public function pivotRoles()
    {
        return $this->hasMany('AuthRoleRule', 'rule_id');
    }

    public function roles()
    {
        return $this->belongsToMany('AuthRole', '\\tpadmin\\model\\AuthRoleRule', 'role_id', 'rule_id');
    }

    public static function onAfterDelete($rule)
    {
        AuthRoleRule::where('rule_id', $rule->id)->delete();
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

    public static function resort($list)
    {
        if(!is_array($list)){
            return false;
        }

        foreach ($list as $id => $sort_num) {
            self::where('id', intval($id))->update(['sort_num' => $sort_num]);
        }
        return true;
    }


    public static function reloadAdminerAllows($is_force)
    {
        $current_adminer = Auth::user();
        if(empty($current_adminer)){
            return false;
        }
    }
}
