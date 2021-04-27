# 常见问题

## 没有 config/apidoc.php 配置文件

如使用`composer2`以上版本，且`topthink/framework`版本小于 V6.0.6，安装时会出现无法自动生成配置文件

#### 解决方案 1

执行 `composer update topthink/framework` 将 thinkphp 主框架升级到最新，再安装本插件

#### 解决方案 2

手动将 `/vendor/hg/apidoc/src/config.php` 拷贝到`/config/`目录下，并重命名为`apidoc.php`

## 404 错误

### 1、 tp 版本为 5.x 安装 apidoc 的版本为 2.x

由于 apidoc2.0 以上版本仅支持 tp6 以上版本，tp5 的用户请使用 apidoc1.x 版本[v1.x](https://hgthecode.github.io/thinkphp-apidoc/v1/install/)

### 2、伪静态配置

通常该问题会报出 `Cannot read property 'config' of undefined` 的错误，这并不是 config 文件不存在，而是 apidoc 会自动注册`/apidoc/config`等一些路由，如果没有正确配置项目伪静态规则，会导致无法正常访问路由

解决方案 1：配置伪静态即可解决

- Apache

```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?$1 [QSA,PT,L]

  SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
</IfModule>
```

- Nginx

```
location / {
    if (!-e $request_filename){
    rewrite ^(.*)$ /index.php?s=$1 last;
    break;
    }
}
```

解决方案 2：配置前端项目请求 host 参数：

> 需升级前端文件到 [v1.2.1 版本]()

```js
// public/apidoc/config.js

var config = {
  // 请求地址host
  HOST: "index.php",
};
```

### 3、tp 项目根目录配置问题

有的用户配置站点目录为项目根目录或更上级目录，而 tp 的入口为 public 目录，就会导致无法正确的访问路由，也会报出 404 `Cannot read property 'config' of undefined` 的错误

解决方案 1：将站点目录配置为 public 目录，并正确配置伪静态。

解决方案 2：配置前端项目请求 host 参数：

> 需升级前端文件到 [v1.2.1 版本]()

```js
// public/apidoc/config.js

var config = {
  // 请求地址host
  HOST: "public/index.php",
};
```

<!-- ## 接口目录为空

如出现控制器/接口都写了注解，但文档页面未显示的问题，请检查一下几个原因

### 1、配置了controllers

由于配置文件`config/apidoc.php`配置了`controllers`之后就只会解析该配置所指定的控制器，请检查未正常显示的控制器是否在该配置中定义

### 2、接口方法 -->

## 500 注解报错

所有文件注释中存在 @XXX 的，都需`use`引入注释解释文件，如出现以下错误

![apidoc-help-use_error](/thinkphp-apidoc/images/apidoc-help-use_error.png "apidoc-help-use_error")

可根据提示在相应的文件里，加上 use 解释文件

```php
<?php
namespace app\controller;

// 加上这句
use hg\apidoc\annotation as Apidoc;
// 通过use自定义解释文件，解决下面@abc的错误
// use app\utils\Abc;

/**
 * @Apidoc\Title("基础示例")
 */
class BaseDemo
{
    /**
     * @Apidoc\Title("引入通用注释")
     * @abc 错误示例，这样不存在解释文件的注释会报错；可增加use解释文件，或去除@
     */
    public function test(){
        //...
    }
}
```

自定义解释文件

```php
// app/utils/Abc.php 解释文件内容
<?php
namespace app\utils;
use Doctrine\Common\Annotations\Annotation;

/**
 * 自定义参数解释文件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Abc extends Annotation
{}
```

更多处理方式可查阅 [doctrine/annotations](https://github.com/doctrine/annotations)

## V1.0 升级到 V2.0 报错

由于 V2.0 是不向下兼容的重构版本，如从 V1.0 升级到 V2.0 可能会出现如下问题

- 未定义数组索引 auth
- 方法不存在 hg\apidoc\Controller::getList()

#### 解决方案

##### 1、升级前端文件

[安装说明-添加前端页面](/install/#添加前端页面) 下载最新的文件覆盖原来的 public/apidoc 目录

##### 2、确认 config/apidoc.php 配置文件为 V2.x 版本的配置文件

参考 [config/apidoc.php](https://github.com/HGthecode/thinkphp-apidoc/blob/master/src/config.php)
