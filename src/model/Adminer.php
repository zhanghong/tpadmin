<?php

namespace tpadmin\model;

use tpadmin\service\auth\facade\Auth;
use tpadmin\validate\AdminerCreate as ValidateCreate;
use tpadmin\validate\AdminerUpdate as ValidateUpdate;
use think\exception\ValidateException;
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

    static public function createItem($data)
    {
        $validate = new ValidateCreate;
        if (!$validate->check($data)) {
            throw new ValidateException($validate->getError());
        }

        $adminer = new self;
        $adminer->save($data);

        return $adminer;
    }

    static public function updateItem($id, $data)
    {
        $adminer = self::find($id);
        if(empty($adminer)){
            return false;
        }

        return $adminer->updateInfo($data);
    }

    public function updateInfo($data)
    {
        $data['id'] = $this->id;
        $validate = new ValidateUpdate;
        if (!$validate->check($data)) {
            throw new ValidateException($validate->getError());
        }

        $this->save($data, ['id' => $this->id]);
        return $this;
    }

    static public function deleteItem($id)
    {
        $current_adminer = Auth::user();
        if(empty($current_adminer)){
            throw new \Exception('请先登录系统');
        }else if($id == $current_adminer->id){
            throw new \Exception('自己不能删除自己账号');
        }

        return self::destroy($id);
    }
}
