---
icon:  help
category: 使用
---

# 常见问题


## 没有config/apidoc.php配置文件
如使用`composer2`以上版本，且`topthink/framework`版本小于 V6.0.6，安装时会出现无法自动生成配置文件

#### 解决方案1：
执行 `composer update topthink/framework` 将thinkphp主框架升级到最新，再安装本插件

#### 解决方案2：
手动将 `/vendor/hg/apidoc/src/config.php` 拷贝到`/config/`目录下，并重命名为`apidoc.php`



## 404错误


### 1、 tp版本为5.x安装apidoc的版本为2.x

由于apidoc2.0以上版本仅支持tp6以上版本，tp5的用户请使用apidoc1.x版本[v1.x](https://hgthecode.github.io/thinkphp-apidoc/v1/install/)

### 2、伪静态配置 

通常该问题会报出 `Cannot read property 'config' of undefined` 的错误，这并不是config文件不存在，而是apidoc会自动注册`/apidoc/config`等一些路由，如果没有正确配置项目伪静态规则，会导致无法正常访问路由

解决方案1：配置伪静态即可解决

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

解决方案2：配置前端项目请求路由前缀参数：
> 需升级前端文件到 [v1.2.1版本]()
```js
// public/apidoc/config.js

var config = {
  // 路由前缀，根据你的项目情况配置
  routePrefix: "index.php"
};
```


### 3、tp项目根目录配置问题

有的用户配置站点目录为项目根目录或更上级目录，而tp的入口为public目录，就会导致无法正确的访问路由，也会报出404 `Cannot read property 'config' of undefined` 的错误

解决方案1：将站点目录配置为public目录，并正确配置伪静态。

解决方案2：配置前端项目请求路由前缀参数：
> 需升级前端文件到 [v1.2.1版本]()
```js
// public/apidoc/config.js

var config = {
  // 路由前缀，根据你的项目情况配置
  routePrefix: "public/index.php"
};
```


## 500注解报错
所有文件注释中存在 @XXX 的，都需`use`引入注释解释文件，如出现以下错误

![apidoc-help-use_error](/thinkphp-apidoc/images/apidoc-help-use_error.png "apidoc-help-use_error")

可根据提示在相应的文件里，加上use解释文件

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


## V1.0升级到V2.0报错
由于V2.0是不向下兼容的重构版本，如从V1.0升级到V2.0可能会出现如下问题
- 未定义数组索引auth
- 方法不存在 hg\apidoc\Controller::getList()

#### 解决方案
##### 1、升级前端文件
 [安装说明-添加前端页面](/install/#添加前端页面) 下载最新的文件覆盖原来的public/apidoc目录

##### 2、确认config/apidoc.php配置文件为V2.x版本的配置文件
参考 [config/apidoc.php](https://github.com/HGthecode/thinkphp-apidoc/blob/master/src/config.php)
