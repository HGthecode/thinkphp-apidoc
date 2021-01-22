# 常见问题

## TP5.x版本文档接口404
TP5.x版本或本插件小于v1.1.1版本的在安装后 访问文档页面出现如下接口404的情况。
> 因为TP5.x版本没有自动注册服务来注册文档所需的接口路由导致的

<img class="img-view" :src="$withBase('/images/v1/apidoc-help-route404.png')" style="width:100%;" alt="apidoc-help-route404">


将以下路由加入到 `route/app.php`
```php
// route/app.php
Route::get('apidoc/config', "\\hg\\apidoc\\Controller@getConfig");
Route::get('apidoc/data', "\\hg\\apidoc\\Controller@getList");
Route::post('apidoc/auth', "\\hg\\apidoc\\Controller@verifyAuth");
```

## 使用了TP6注解扩展报错
如果使用了TP6的注解扩展`topthink/think-annotation`，书写注释后，导致类似如下错误
```sh
[Semantical Error] The annotation \"@title\" in class app\\controller\\BaseDemo was never imported. Did you maybe forget to add a \"use\" statement for this annotation?
```
可在相应的控制器use解释文件消除错误

```php
<?php
namespace app\controller;

use hg\apidoc\annotation\explain\Title;
use hg\apidoc\annotation\explain\Desc;
use hg\apidoc\annotation\explain\Controller;
use hg\apidoc\annotation\explain\Author;
use hg\apidoc\annotation\explain\Url;
use hg\apidoc\annotation\explain\Tag;
use hg\apidoc\annotation\explain\Header;
use hg\apidoc\annotation\explain\Param;
use hg\apidoc\annotation\explain\ParamType;

/**
 * @title 基础示例
 * @controller BaseDemo
 * @group base
 */
class BaseDemo
{
    //...
}
```
更多处理方式可查阅 [doctrine/annotations](https://github.com/doctrine/annotations) 


