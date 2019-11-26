<?php

namespace tpadmin\model;

use think\auth\model\RoleRule as ThinkRoleRule;

class RoleRule extends ThinkRoleRule
{
    protected $autoWriteTimestamp = false;

    protected $name = "AuthRoleRule";
}
