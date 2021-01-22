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
    ['title'=>'V1.0','folder'=>'app\controller\v1'],
    ['title'=>'V2.0','folder'=>'app\controller\v2']
],
```

如配置了 `controllers` 生成文档的控制器目录调整为：
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
其原理为 当前版本的folder + 控制器 来生成接口文档
:::


## 控制器分组

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

use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\title("基础示例")
 * @Apidoc\group("base")
 */
class ApiDocTest
{ 
```



## 密码验证
配置文件`/config/apidoc.php`中的 auth 设置如下，即可在访问文档页面时需输入密码访问：
```php
// /config/apidoc.php
// 权限认证配置
'auth' => [
    // 是否启用密码验证
    'enable'     => true,
    // 验证密码
    'password'   => "123456",
    // 密码加密盐
    'secret_key' => "apidoc#hg_code",
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
// 缓存配置
'cache' => [
    // 是否开启缓存
    'enable' => false,
    // 缓存文件路径
    'path'   =>  '../runtime/apidoc/',
    // 是否显示更新缓存按钮
    'reload' => true,
    // 最大缓存文件数
    'max'    => 5,  //最大缓存数量
],
```

缓存的文件默认生成在 `/runtime/apidoc/`目录下，如需删除缓存记录，可直接删除该目录下的相应文件即可

## Markdown 文档

1、根目录下创建`docs`（你也可以是别的）目录，并创建md文档文件，如下
```sh
app
config
docs
 |—— Use.md
 |—— V1.0
    |—— HttpStatus.md
    |—— HttpCode.md
 |—— V2.0
    |—— HttpStatus.md
    |—— HttpCode.md 
 ...
```

2、配置文档菜单

> 可使用 `{:version}` 做多版本区分

```php
// config/apidoc.php
<?php
return [
    // markdown 文档
    'docs' => [
        'menu_title' => '开发文档',
        'menus'      => [
            ['title'=>'使用说明','path'=>'docs/Use'],
            [
                'title'=>'HTTP响应码',
                'items'=>[
                    ['title'=>'status错误码说明','path'=>'docs/{:version}/HttpStatus'],
                    ['title'=>'code错误码说明','path'=>'docs/{:version}/HttpCode'],
                ],
            ]
        ]

    ]
]
```

<img class="img-view" :src="$withBase('/images/v2/apidoc-demo-md.png')" style="width:100%;" alt="apidoc-demo-md">




