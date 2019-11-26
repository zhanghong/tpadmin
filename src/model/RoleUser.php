<?php

namespace tpadmin\model;

use think\auth\model\RoleUser as ThinkRoleUser;

class RoleUser extends ThinkRoleUser
{
    protected $autoWriteTimestamp = false;

    protected $name = "AuthRoleUser";
}
