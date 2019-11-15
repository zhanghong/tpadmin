<?php

namespace tpadmin\service\auth\facade;

use think\Facade;

class Auth extends Facade
{
    protected static function getFacadeClass()
    {
        return \tpadmin\service\auth\Auth::class;
    }
}