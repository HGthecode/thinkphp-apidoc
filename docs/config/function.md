# 功能配置

## 多版本

在单应用中，我们通常使用这样的目录接口来实现多版本接口开发
```sh
app
 |—— controller
    |—— v1
        BaseDemo.php
        CrudDemo.php
        ...
    |—— v2
        BaseDemo.php
        CrudDemo.php
        ...
 |—— model
 ...
```

根据以上项目的目录结构，在配置文件`/config/apidoc.php`中的 versions 参数中配置你的可选版本，如下
```php
// /config/apidoc.php
// 设置可选版本
'versions'=>[
    ['title'=>'V1.0','folder'=>'controller\\v1'],
    ['title'=>'V2.0','folder'=>'controller\\v2']
],
```

配置文件`/config/apidoc.php`中的 controllers 生成文档的控制器目录调整为：
```php
// /config/apidoc.php
//生成文档的控制器
'controllers' => [
    'BaseDemo',
    'CrudDemo',
],
```

::: warning folder
多版本配置的关键点在于 folder 目录的配置，请根据你的项目结构调整，
其原理为 app文件夹开始， 当前版本的folder + 配置的controllers中的控制器 来生成接口文档
:::


## 控制器分组  -new
> 支持版本 >= v1.1.1

可通过控制器分组实现将多模块的控制器进行分组

1、配置文件`/config/apidoc.php`中的 groups 配置分组列表：
```php
// /config/apidoc.php
//设置控制器分组
'groups'=>[
    ['title'=>'基础模块','name'=>'base'],
    ['title'=>'示例模块','name'=>'demo'],
],
```

2、在对应控制器注释中，加入 `@group` 来指定该控制器所属分类的 name
```php
namespace app\controller;
/**
 * @title Api接口文档测试
 * @controller ApiDocTest
 * @group base
 */
class ApiDocTest
{ 
```



## 密码验证
配置文件`/config/apidoc.php`中的 auth 设置如下，即可在访问接口文档时需输入密码访问：
```php
// /config/apidoc.php
// 密码验证配置
    'auth'=>[
    // 是否启用密码验证
    'with_auth'=>true,
    // 验证密码
    'auth_password'=>"123456",
],
```

## 文档缓存
::: warning 建议
开发环境中关闭缓存，可方便实时修改查看效果，提升开发效率。正式发布后可开启。
:::

开启缓存后，每次访问接口文档则直接使用缓存数据展示文档，大大提升访问速度，也可在页面右上角切换历史文档。
但当修改了接口注释后，需手动点击接口文档页面右上角的 `ReLoad` 按钮生成新的文档数据。

配置文件`/config/apidoc.php`中的 with_cache 设置为 `true`
```php
// /config/apidoc.php
// 是否开启缓存
'with_cache'=>true,
],
```

缓存的文件生成在 `/runtime/apidoc/`目录下，如需删除缓存记录，可直接删除该目录下的相应文件即可



