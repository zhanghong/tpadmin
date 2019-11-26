<?php

namespace tpadmin\controller;

use think\App;
use think\app\Url;
use think\facade\View;
use think\facade\Config;
use think\facade\Session;

use tpadmin\model\Rule as RuleModel;
use tpadmin\service\auth\facade\Auth;

abstract class Controller
{
    protected $request;

    protected $app;

    protected $middleware = [];

    protected $viewPath = '';

    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    public function initialize()
    {
        $this->setViewPath();

        $this->assignCommon();
    }

    /**
     * 设置视图模板路径
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-09-10
     */
    public function setViewPath()
    {
        $config = Config::get('tpadmin.template');
        $this->app->config->set($config, 'view');

        $assetsName = '/static/assets';
        if (isset($config['tpl_replace_string']) && isset($config['tpl_replace_string']['__TPADMIN_ASSETS__'])) {
            $assetsName = $config['tpl_replace_string']['__TPADMIN_ASSETS__'];
        }
        $assetsName = ltrim($assetsName, '/');
        $publicName = trim(config('tpadmin.template.public_name'), '/');
        $documentPath = $this->app->getRootPath();

        if (!empty($publicName)) {
            $documentPath .= $publicName.'/';
        }

        if (!file_exists($documentPath.$assetsName)) {
            throw new \Exception('Resource not published,Please initialize tpadmin.');
        }
    }

    /**
     * 控制方法共用内容
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-09-10
     * @return   [type]             [description]
     */
    public function assignCommon()
    {
        $route_app = app(RuleModel::class);
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

        View::assign(compact('menu_tree', 'current_rule', 'current_adminer', 'flash'));
    }

    /**
     * 预处理搜索表单数据
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-09-10
     * @param    Request            $request       请求对象
     * @param    array              $search_fields 参数列表
     * @return   array                             预处理后的参数值列表
     */
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

    /**
     * 预处理表单提交数据
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-06-10
     * @param    Request            $request 请求对象
     * @param    array              $attrs   参数列表
     * @return   array                       预处理后的表单参数值列表
     */
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

    /**
     * 解析和获取模板内容 用于输出
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-11-01
     * @param    string             $template 模板文件名或者内容
     * @param    array              $vars     模板变量
     * @return   string
     */
    protected function fetch(string $template = '', array $vars = []): string
    {
        return View::fetch($template, $vars);
    }

    /**
     * 操作成功跳转的快捷方法
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-11-01
     * @param    string             $msg  提示信息
     * @param    string/Route       $url  跳转的URL地址
     * @param    string             $data 返回的数据
     * @return
     */
    protected function success($msg = '', $url = null, $data = '')
    {
        return $this->jump(1, $msg, $url, $data);
    }

    /**
     * 操作失败跳转的快捷方法
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-11-01
     * @param    string             $msg  提示信息
     * @param    string/Route       $url  跳转的URL地址
     * @param    string             $data 返回的数据
     * @return
     */
    protected function error($msg = '', $url = null, $data = '')
    {
        return $this->jump(0, $msg, $url, $data);
    }

    /**
     * URL重定向
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-11-01
     * @param    string/Route       $url    跳转的URL表达式
     * @param    integer            $code   http code
     * @return   void
     */
    protected function redirect($url, int $code = 302)
    {
        if ($url instanceof Url) {
            $url = (string) $url;
        } else if (!(strpos($url, '://') || 0 === strpos($url, '/'))) {
            // buildUrl 方法返回值是 think\app\Url 对象，所以必须强制转化成字符串
            $url = (string) $this->app->route->buildUrl($url);
        }

        return redirect($url);
    }

    /**
     * 操作跳转方法
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-11-01
     * @param    integer            $code 是否成功
     * @param    string             $msg  提示信息
     * @param    string/Route       $url  跳转的URL地址
     * @param    string             $data 返回的数据
     * @return
     */
    private function jump($code, $msg = '', $url = null, $data = '')
    {
        if (is_null($url)) {
            $url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
        } else if ($url instanceof Url) {
            $url = (string) $url;
        } else if (!(strpos($url, '://') || 0 === strpos($url, '/'))) {
            // buildUrl 方法返回值是 think\app\Url 对象，所以必须强制转化成字符串
            $url = (string) $this->app->route->buildUrl($url);
        }

        if ($this->request->isAjax()){
            return json([
                'code' => $code,
                'msg' => $msg,
                'data' => $data,
                'url' => $url,
            ]);
        }

        return $this->redirect($url);
    }
}
