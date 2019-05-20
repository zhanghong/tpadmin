<?php

namespace tpadmin\controller;

use think\Request;
use think\exception\ValidateException;

use tpadmin\model\Config as ConfigModel;

class Config extends Controller
{
    public function site(Request $request)
    {
        $name = ConfigModel::NAME_SITE_SETTING;
        if($request->isPost()){
            $filter_attrs = [
                ['name' => 'title', 'type' => 'string', 'default' => ''],
                ['name' => 'keywords', 'type' => 'string', 'default' => ''],
                ['name' => 'description', 'type' => 'string', 'default' => ''],
                ['name' => 'icp', 'type' => 'string', 'default' => ''],
                ['name' => 'copyright', 'type' => 'string', 'default' => ''],
                ['name' => 'tongji', 'type' => 'string', 'default' => ''],
            ];

            $error_msg = NULL;
            $settings = $this->filterPostData($request, $filter_attrs);
            try{
                $config = ConfigModel::createOrUpdateByName($name, $settings);
            } catch (ValidateException $e) {
                $error_msg = $e->getError();
            } catch (\Exception $e) {
                $error_msg = $e->getError();
            }

            if(!is_null($error_msg)){
                return json($error_msg);
                $this->error($error_msg);
            }
            return $this->success('更新成功');
        }else{
            $config = ConfigModel::findOrCreateByName($name);
            $this->assign('settings', $config->settings);
            return $this->fetch('config/site');
        }
    }
}