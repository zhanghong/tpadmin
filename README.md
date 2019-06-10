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

1、安装 Tpadmin :
```shell
$ composer require zhanghong/tpadmin
```

2、配置

添加行为在 `application/tags.php`

```
return [

    'app_init'     => [
        \tpadmin\behavior\Boot::class,
    ],

    // ...
];
```

3、初始化和数据迁移
```shell
#初始化
$ php think tpadmin:init
#安装数据库迁移扩展包
$ composer require topthink/think-migration=2.0.*
#创建Tpadmin数据表
$ php think migrate:run
#添加初始化数据
$ php think tpadmin:seed
```

## 进入tpadmin后台

打开后台地址，例如：

http://yourdomain/admin