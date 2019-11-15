<?php

namespace tpadmin\controller;

use think\Request;
use think\facade\Session;
use tpadmin\exception\ValidateException;

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
            }catch (ValidateException $e){
                return $this->error($e->getMessage(), '', ['errors' => $e->getData()]);
            }catch (\Exception $e){
                return $this->error($e->getMessage());
            }

            $success_message = '更新成功';
            Session::flash('success', $success_message);
            return $this->success($success_message);
        }else{
            $config = ConfigModel::findOrCreateByName($name);
            return $this->fetch('config/site', [
                'settings' => $config->settings,
            ]);
        }
    }
}