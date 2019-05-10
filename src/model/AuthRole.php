<?php

namespace tpadmin\model;

class AuthRole extends Model
{
    public function rules()
    {
        return $this->belongsToMany('AuthRule', '\\tpadmin\\model\\AuthRoleRule', 'rule_id', 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany('Adminer', '\\tpadmin\\model\\AuthRoleUser', 'user_id', 'role_id');
    }
}
