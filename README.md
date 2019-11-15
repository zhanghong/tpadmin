<h1 align="center"> tpadmin </h1>

<p align="center"> the admin framework based on thinkphp 5.1.</p>
<p align="center">
  <a href="https://styleci.io/repos/177950338">
    <img src="https://styleci.io/repos/177950338/shield?branch=master" alt="StyleCI">
  </a>
   <a href="https://travis-ci.org/zhanghong/tpadmin">
      <img src="https://travis-ci.org/zhanghong/tpadmin.svg?branch=master" alt="TravisCi">
  </a>
</p>

## 安装
最方便的安装方式就是使用Composer ( https://getcomposer.org/ )，在这之前**务必**先搭建好thinkphp5.1项目

1 安装 Tpadmin :
```shell
$ composer require zhanghong/tpadmin
```

2. 容器Provider

在项目或应用的容器Provider添加以下定义（项目配置文件是：app/provider.php，应用配置文件是：app/app_name/provider.php）：


```php
<?php
return [
    .
    .
    .
    \tpadmin\service\upload\contract\Factory::class => \tpadmin\service\upload\Uploader::class,
    \tpadmin\service\auth\contract\Authenticate::class => \tpadmin\model\Adminer::class,
    \tpadmin\service\auth\guard\contract\Guard::class => \tpadmin\service\auth\guard\SessionGuard::class,
    \tpadmin\service\auth\contract\Auth::class => \tpadmin\service\auth\Auth::class,
];
```

3. 配置自定义命令

在配置文件(config/console.php)里注册扩展包的自定义命令

*config/console.php*
```php
<?php
return [
    'commands' => [
        .
        .
        .
        // 可以把tpadmin:init:seed修改任何你喜欢的名称
        'tpadmin:init' => 'tpadmin\command\Init',
        'tpadmin:seed' => 'tpadmin\command\Seed',
    ],
];
```

4. 初始化和数据迁移

```shell
#创建Tpadmin数据表
$ php think migrate:run
# 初始化(与注册的自定义命令保持一致)
$ php think tpadmin:init
#添加初始化数据(与注册的自定义命令保持一致)
$ php think tpadmin:seed
```

## 进入tpadmin后台

后台登录地址是 `http://yourdomain/admin` ，扩展包安装成功后自带了两个管理员账号（admin:123456, manager:123456）。