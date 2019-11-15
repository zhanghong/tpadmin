<?php

namespace tpadmin\command;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Config;

class Init extends Command
{
    protected $app_root_path;
    protected $module_name;

    protected function configure()
    {
        $this->setName('tpadmin:init')->setDescription('init tpadmin');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->app_root_path = app()->getRootPath();
        $this->publishConfig();
        $this->publishRoute();
        $this->publishMigrations();
        $this->publishAssets();
        $this->publishAdminView();
    }

    /**
     * 发布资源文件
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   bool
     */
    private function publishAssets()
    {
        $cfg = Config::get('tpadmin.');

        $assets = 'static/assets';
        $publicName = 'public';
        if(isset($cfg['template'])){
            $tpl = $cfg['template'];

            if(isset($tpl['public_name'])){
                $publicName = trim($tpl['public_name'], '/');
            }

            if(isset($tpl['tpl_replace_string']) && isset($tpl['tpl_replace_string']['__TPADMIN_ASSETS__'])){
                $assets = ltrim($tpl['tpl_replace_string']['__TPADMIN_ASSETS__'], '/');
            }
        }

        $source = new Filesystem(
            new Local(__DIR__.'/../../resource/assets')
        );

        $rootPath = $this->app_root_path;
        $traget = new Filesystem(
            new Local($rootPath . $publicName .'/' .$assets)
        );

        return $this->copyLocalDir($source, $traget);
    }

    /**
     * 发布配置文件
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   bool
     */
    private function publishConfig()
    {
        $source = new Filesystem(
            new Local(__DIR__.'/../../resource/config')
        );
        $traget = new Filesystem(
            new Local($this->app_root_path.'config')
        );

        return $this->copyLocalDir($source, $traget);
    }

    /**
     * 发布路由文件
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   bool
     */
    private function publishRoute()
    {
        $source = new Filesystem(
            new Local(__DIR__.'/../../resource/route')
        );
        $traget = new Filesystem(
            new Local($this->app_root_path.'route/admin')
        );

        return $this->copyLocalDir($source, $traget);
    }

    /**
     * 复制数据库迁移文件
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   bool
     */
    private function publishMigrations()
    {
        $source = new Filesystem(
            new Local(__DIR__.'/../../resource/migrations')
        );
        $traget = new Filesystem(
            new Local($this->app_root_path.'database/migrations/')
        );

        return $this->copyLocalDir($source, $traget);
    }

    /**
     * 发布管理员视图页面
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @return   bool
     */
    private function publishAdminView()
    {
        $source = new Filesystem(
            new Local(__DIR__.'/../../resource/view')
        );
        $traget = new Filesystem(
            new Local($this->app_root_path.'app/admin/view/')
        );

        return $this->copyLocalDir($source, $traget);
    }

    /**
     * 复制文件夹
     * @Author   zhanghong(Laifuzi)
     * @DateTime 2019-05-24
     * @param    Local              $source_local 来源文件夹
     * @param    Local              $traget_local 目标文件夹
     * @return   bool
     */
    private function copyLocalDir($source_local, $traget_local)
    {
        $manager = new MountManager([
            'source' => $source_local,
            'traget' => $traget_local,
        ]);

        $contents = $manager->listContents('source://', true);

        foreach ($contents as $entry) {
            $update = false;

            if (!$manager->has('traget://'.$entry['path'])) {
                $update = true;
            } elseif ($manager->getTimestamp('source://'.$entry['path']) > $manager->getTimestamp('traget://'.$entry['path'])) {
                $update = true;
            }

            if ('file' === $entry['type'] && $update) {
                $manager->put('traget://'.$entry['path'], $manager->read('source://'.$entry['path']));
            }
        }
        return true;
    }
}