<?php

namespace tpadmin\controller;

use think\Loader;
use think\Console;
use think\Container;
use think\facade\Config;
use think\facade\Session;
use think\Controller as ThinkController;

use tpadmin\model\AuthRule as AuthRuleModel;
use tpadmin\service\auth\facade\Auth;

abstract class Controller extends ThinkController
{
    protected $viewPath = '';

    public function __construct()
    {
        parent::__construct();

        // $this->initConfig();

        $this->setViewPath();

        $this->assignCommon();
    }

    // public function initConfig()
    // {
    //     if (is_file(admin_config_path('paginate.php'))) {
    //         $paginateAdmin = include admin_config_path('paginate.php');
    //         $config = Container::get('config');
    //         $paginate = $config->pull('paginate');
    //         $config->set(array_merge(
    //             \is_array($paginate) ? $paginate : [],
    //             $paginateAdmin
    //         ), 'paginate');
    //     }
    // }

    public function setViewPath()
    {
        $view_path = config('tpadmin.template.view_path');
        $this->viewPath = $this->app->getAppPath() . DIRECTORY_SEPARATOR . $view_path;
        $this->view->config('view_path', $this->viewPath);
        $this->view->config('tpl_replace_string', config('tpadmin.template.tpl_replace_string'));
        $assets = ltrim(config('tpadmin.template.tpl_replace_string.__TPADMIN_ASSETS__'), '/');
        $publicName = trim(config('tpadmin.template.public_name'), '/');
        $documentPath = Loader::getRootPath();

        if (!empty($publicName)) {
            $documentPath .= $publicName.'/';
        }

        if (!file_exists($documentPath.$assets)) {
            throw new \Exception('Resource not published,Please initialize tpadmin.');
        }
    }

    public function assignCommon()
    {
        $route_app = app(AuthRuleModel::class);
        $menu_tree = $route_app->toTree();
        $flat_menu = $route_app->flatMenuTree($menu_tree);

        $current_rule = NULL;
        $current_route_name = current_request_route();
        if(!empty($current_route_name) && isset($flat_menu[$current_route_name])){
            $current_rule = $flat_menu[$current_route_name];
        }

        $current_adminer = Auth::user();
        $this->current_adminer = $current_adminer;

        $flash = [];
        $flash_names = ['success', 'info', 'danger'];
        foreach ($flash_names as $key => $name) {
            if(Session::has($name)){
                $flash[$name] = Session::get($name);
            }
        }
        $this->view->assign(compact('menu_tree', 'current_rule', 'current_adminer', 'flash'));
    }

    protected function filterSearchData($request, $search_fields)
    {
        $data = [];
        foreach ($search_fields as $key => $field) {
            if(!isset($field['param_name'])){
                continue;
            }
            $param_name = $field['param_name'];
            $value = $request->get($param_name);
            if(is_null($value) || $value == ''){
                continue;
            }
            $data[$param_name] = $value;
        }
        return $data;
    }

    protected function filterPostData($request, $attrs)
    {
        $data = [];
        $allow_types = ['string', 'integer', 'date', 'float'];
        foreach ($attrs as $key => $attr) {
            if(!isset($attr['name'])){
                continue;
            }
            $name = $attr['name'];

            if(!isset($attr['type']) || !in_array($attr['type'], $allow_types)){
                $type = 'string';
            }else{
                $type = $attr['type'];
            }


            $default = NULL;
            $filter = '';

            switch ($type) {
                case 'integer':
                    $default = 0;
                    $filter = 'intval';
                    break;
                case 'float':
                    $default = 0;
                    $filter = 'floatval';
                    break;
                case 'date':
                    $filter = '';
                    break;
                default:
                    $default = '';
                    $filter = 'trim';
                    break;
            }

            if(isset($attr['default'])){
                $default = $attr['default'];
            }

            if(isset($attr['filter'])){
                $filter = $attr['filter'];
            }

            $data[$name] = $request->post($name, $default, $filter);
        }
        return $data;
    }
}