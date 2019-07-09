<?php

namespace tpadmin\model;

use Db;
use think\Model as Base;
use tpadmin\exception\ValidateException;

abstract class Model extends Base
{
    protected static function baesCreateItem(array $data, $validate = NULL)
    {
        if (!empty($validate) && !$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $item = self::create($data);
        return $item->reload();
    }

    protected static function baesUpdateItem($id, array $data, $validate = NULL, $only_allow = true)
    {
        $id = intval($id);
        $item = self::find($id);
        if(empty($item)){
            throw new \Exception('未找到更新记录');
        }

        $data['id'] = $id;
        return $item->runUpdate($data, $validate, $only_allow);
    }

    protected function runUpdate($data, $validate = NULL, $only_allow = true)
    {
        if (!empty($validate) && !$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $this->allowField($only_allow)->save($data, ['id' => $this->id]);
        return $this;
    }

    protected function reload()
    {
        return $this->find($this->id);
    }

    protected static function queryConditions($params)
    {
        $search_fields = static::searchFields();

        $map = [];
        foreach ($search_fields as $key => $field) {
            $param_name = $field['param_name'];
            if(!isset($params[$param_name])){
                continue;
            }

            $param_value = trim($params[$param_name]);
            if(empty($param_value)){
                continue;
            }

            $type = '';
            if(isset($field['type'])){
                $type = $field['type'];
            }
            switch ($type) {
                case 'integer':
                    $param_value = intval($param_value);
                    break;
                case 'float':
                    $param_value = floatval($param_value);
                    break;
            }

            $mode = '';
            if(isset($field['mode'])){
                $mode = $field['mode'];
            }

            if(isset($field['column_name'])){
                $name = $field['column_name'];
            }else{
                $name = $param_name;
            }
            switch ($mode) {
                case 'like':
                    array_push($map, [$name, 'LIKE', '%'.$param_value.'%']);
                    break;
                default:
                    array_push($map, [$name, '=', $param_value]);
                    break;
            }
        }
        return $map;
    }

    public static function searchFields()
    {
        return [];
    }
}
