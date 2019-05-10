<?php

namespace tpadmin\model;

use Db;
use think\Model as Base;
use think\exception\ValidateException;

abstract class Model extends Base
{
    static protected function baesCreateItem(array $data, $validate = NULL)
    {
        if (!empty($validate) && !$validate->check($data)) {
            throw new ValidateException($validate->getError());
        }

        return self::create($data);
    }

    static protected function baesUpdateItem($id, array $data, $validate = NULL)
    {
        $id = intval($id);
        $item = self::find($id);
        if(empty($item)){
            throw new \Exception('未找到更新记录');
        }

        return $item->runUpdate($data, $validate);
    }

    protected function runUpdate($data, $validate = NULL)
    {
        if (!empty($validate) && !$validate->check($data)) {
            throw new ValidateException($validate->getError());
        }

        $this->save($data, ['id' => $this->id]);
        return $this;
    }
}
