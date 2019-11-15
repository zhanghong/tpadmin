<?php

declare(strict_types=1);

namespace tpadmin\model;

use tpadmin\validate\Config as Validate;

class Config extends Model
{
    const NAME_SITE_SETTING = 'site_setting';

    public function setValueAttr($value)
    {   if(is_string($value)){
            return $value;
        }
        return json_encode($value);
    }

    public function getSettingsAttr()
    {
        if(empty($this->value)){
            return [];
        }
        return json_decode($this->value, true);
    }

    public static function findOrCreateByName($name)
    {
        $item = self::where('name', $name)->find();
        if(!empty($item)){
            return $item;
        }

        $data = [
            'name' => $name,
            'value' => [],
        ];
        $validate = new Validate;
        $item = self::baesCreateItem($data, $validate);
        return $item;
    }

    public static function createOrUpdateByName($name, $value)
    {
        $data = [
            'name' => $name,
            'value' => $value,
        ];
        $validate = new Validate;

        $item = self::where('name', $name)->find();
        if(empty($item)){
            $item = self::baesCreateItem($data, $validate);
        }else{
            $data['id'] = $item->id;
            $item->runUpdate($data, $validate);
        }
        return $item;
    }
}