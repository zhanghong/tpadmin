<?php

namespace tpadmin\model;

class Role extends Model
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, RolePermission::class);
    }
}
