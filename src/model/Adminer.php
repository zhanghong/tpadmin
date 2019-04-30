<?php

namespace tpadmin\model;

use tpadmin\service\auth\contract\Authenticate;

class Adminer extends Model implements Authenticate
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, AdminerRole::class);
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
}
