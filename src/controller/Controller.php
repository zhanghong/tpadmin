<?php

namespace tpadmin\controller;

use think\Loader;
use think\Console;
use think\Container;
use think\facade\Config;
use think\Controller as ThinkController;

use tpadmin\model\AuthRule;
use tpadmin\service\auth\facade\Auth;

abstract class Controller extends ThinkController
{
    protected $viewPath = '';

    public function __construct()
    {
        parent::__construct();

        $this->initConfig();

        $this->setViewPath();

        $this->assignCommon();
    }

    public function initConfig()
    {
        if (is_file(admin_config_path('paginate.php'))) {
            $paginateAdmin = include admin_config_path('paginate.php');
            $config = Container::get('config');
            $paginate = $config->pull('paginate');
            $config->set(array_merge(
                \is_array($paginate) ? $paginate : [],
                $paginateAdmin
            ), 'paginate');
        }
    }

    public function setViewPath()
    {
        $this->viewPath = config('tpadmin.template.view_path');
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
        $menu_tree = app(AuthRule::class)->toTree();
        $current_adminer = Auth::user();
        $this->current_adminer = $current_adminer;
        $current_ancestor_ids = [];
        $this->view->assign(compact('menu_tree', 'current_adminer', 'current_ancestor_ids'));
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