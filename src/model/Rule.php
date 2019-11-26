<?php

namespace tpadmin\model;

use think\auth\model\Rule as ThinkRule;
use tpadmin\service\auth\facade\Auth;
use tpadmin\validate\Rule as Validate;
use tpadmin\exception\ValidateException;

class Rule extends Model
{
    const MENU_MODE_ALL = 'all_menu';
    const MENU_MODE_USER = 'user_menu';

    const USER_ALLOWS = 'user_rules';

    use traits\Tree;

    protected $name = "AuthRule";

    public static function onAfterDelete($rule)
    {
        RoleRule::where('rule_id', $rule->id)->delete();
    }

    public static function createItem($data)
    {
        $validate = new Validate;
        if (!empty($validate) && !$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        return static::create($data);
    }

    public static function updateItem($id, $data)
    {

        $id = intval($id);
        $rule = static::find($id);
        if(empty($rule)){
            throw new \Exception('未找到更新记录');
        }

        $data['id'] = $id;
        $validate = new Validate;
        if (!empty($validate) && !$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $rule->save($data);
        return $rule;
    }

    public static function resort($list)
    {
        if(!is_array($list)){
            return false;
        }

        foreach ($list as $id => $sort_num) {
            static::where('id', intval($id))->update(['sort_num' => $sort_num]);
        }
        return true;
    }
}
