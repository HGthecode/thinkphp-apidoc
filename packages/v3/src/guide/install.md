---
icon: install
category: 指南
sidebarDepth: 2
---

# 安装/升级

::: tip 在安装本插件时，确保你已成功安装ThinkPHP的项目并成功运行
安装方法参考：[ThinkPHP5文档](https://www.kancloud.cn/manual/thinkphp5_1/353948)
[ThinkPHP6文档](https://www.kancloud.cn/manual/thinkphp6_0/1037481)
:::



## 安装插件
进入ThinkPHP 项目根目录，执行如下命令：
```sh
composer require hg/apidoc
```

> TP5版本需[手动添加apidoc所需路由](#tp5添加路由)，如未自动生成config/apidoc.php 配置文件，可参考 [没有生成apidoc.php配置文件解决方案](/use/help/notConfig/)


## 添加前端页面

请根据你安装的apidoc版本 点击下载 对应的前端文件

|Apidoc版本|Github|Gitee（国内推荐）|
|-|-|-|
|`v3.1.0 - v3.1.5`|[v2.1.3](https://github.com/HGthecode/apidoc-ui/releases/download/v2.1.3/apidoc.zip)| [v2.1.3](https://gitee.com/hg-code/apidoc-ui/attach_files/1005160/download/apidoc.zip)|
|`v3.0.0 - v3.0.8`|[v2.0.11](https://github.com/HGthecode/apidoc-ui/releases/download/v2.0.11/apidoc.zip)| [v2.0.11](https://gitee.com/hg-code/apidoc-ui/attach_files/920303/download/apidoc.zip)|

下载完成后解压，将apidoc文件夹拷贝到你的项目 public 目录下

打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功。

接下来，了解一下  [基本配置](/config/) 和 [如何使用](/use/) 吧。

>如遇页面报错或无法正常使用，可参考[常见问题](/use/help/)


## TP5添加路由

TP5版本需手动将apidoc所需路由添加到路由文件

```php
// route/route.php

Route::group('apidoc', function () {
    $controller_namespace = '\hg\apidoc\Controller@';
    Route::get('config'     , $controller_namespace . 'getConfig');
    Route::get('apiData'     , $controller_namespace . 'getApidoc');
    Route::get('mdMenus'     , $controller_namespace . 'getMdMenus');
    Route::get('mdDetail'     , $controller_namespace . 'getMdDetail');
    Route::post('verifyAuth'     , $controller_namespace . 'verifyAuth');
    Route::post('generator'     , $controller_namespace . 'createGenerator');
});
```


## 升级插件

1、如果你之前已经安装过，那么切换到你的应用根目录下面，然后执行下面的命令进行更新。
```sh
composer update hg/apidoc
```
2、更新前端页面，可通过安装时的[添加前端页面](/install/#添加前端页面) 方式下载最新的前端文件，覆盖/public/apidoc的文件夹即可。

::: warning TP5升级注意
TP5.1项目需对照一下上面的[TP5添加路由](#tp5添加路由)，手动添加的路由是否有变化，升级时覆盖一下
:::
