<?php

namespace tpadmin\behavior;

use think\App;
use think\facade\Route;

/**
 * Tpadmin 启动行为.
 */
class Boot
{
    /**
     * @var App
     */
    protected $app;

    /**
     * 路由分组名.
     *
     * @var string
     */
    protected $name = 'admin';

    /**
     * 控制器命名空间.
     *
     * @var string
     */
    protected $namespace = '\\tpadmin\\controller\\';

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handle(App $app, $event)
    {
        // $this->loadHelper();
        // $this->loadConfig();
        $this->importMiddleware();
        // $this->bindProviders();
        // $this->bootRoute();

        $this->initConsole();
    }

    // protected function loadHelper()
    // {
    //     // 加载公共文件
    //     if (is_file(dirname(dirname(__DIR__)) . '/helper.php')) {
    //         include_once dirname(dirname(__DIR__)) . '/helper.php';
    //     }
    // }

    // protected function loadConfig()
    // {
    //     $configFileNames = [
    //         'filesystems',
    //         'tpadmin',
    //     ];
    //     foreach ($configFileNames as $fileName) {
    //         if (is_file(admin_config_path($fileName.'.php'))) {
    //             $file = admin_config_path($fileName.'.php');
    //             $configName = pathinfo($file, PATHINFO_FILENAME);
    //             $config = $this->app->config->pull($configName);

    //             $config = array_deep_merge(
    //                 require_once $file,
    //                 $config
    //             );

    //             $this->app->config->set($config, $configName);

    //             // 重新加载应用中的同名配置，以覆盖此配置
    //             // if (is_file($this->app->getConfigPath().basename($file))) {
    //             //     $this->app->config->load($this->app->getConfigPath().basename($file), $configName);
    //             // }
    //         }
    //     }
    // }

    protected function importMiddleware()
    {
        if (is_file(admin_config_path('middleware.php'))) {
            $this->app->middleware->import(include admin_config_path('/middleware.php'));

            // if (\is_array($middlewares)) {
            //     $this->app->middleware->import($middlewares, 'global');
            // }
        }
    }

    protected function bindProviders()
    {
        if (is_file(admin_config_path('provider.php'))) {
            $this->app->bindTo(
                include_once admin_config_path('/provider.php')
            );
        }
    }

    // protected function bootRoute()
    // {
    //     $routePath = admin_route_path();
    //     // 路由检测
    //     $files = scandir($routePath);
    //     foreach ($files as $file) {
    //         if (strpos($file, '.php')) {
    //             $filename = $routePath.$file;
    //             // 导入路由配置
    //             $this->app->route->group($this->name, function () use ($filename) {
    //                 $rules = include_once $filename;
    //                 if (\is_array($rules)) {
    //                     $this->app->route->import($rules);
    //                 }
    //             })->prefix($this->namespace);
    //         }
    //     }
    // }

    public function initConsole()
    {
        if (!('cli' === \PHP_SAPI || 'phpdbg' === \PHP_SAPI)) {
            return;
        }
    }
}