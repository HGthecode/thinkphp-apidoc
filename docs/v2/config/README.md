---
sidebarDepth: 2
---
# 配置参数
::: tip
安装插件后会在 /config/ 目录下生成一个 apidoc.php 的配置文件，以下为该文件可配置的参数说明，
默认配置文件请查看 [apidoc.php](https://github.com/HGthecode/thinkphp-apidoc/blob/master/src/config.php)
:::

## title
- 类型: string
- 默认值: APi接口文档

页面的标题，显示在左上角与封面页

## desc
- 类型: string

文档说明，文档封面处显示

## copyright
- 类型: string
- 默认值: Powered By HG

页面的版权申明，显示在封面页


## versions
- 类型: Array
- 默认值: undefined

设置API文档的版本
```php
// config/apidoc.php
<?php
return [
    //设置可选版本
    'versions'=>[
        ['title'=>'V1.0','folder'=>'v1'],
        ['title'=>'V2.0','folder'=>'v2'],
    ],
]
```

## groups  
- 类型: Array
- 默认值: undefined

设置控制器分组
```php
// config/apidoc.php
<?php
return [
    //设置控制器分组
    'groups'=>[
        ['title'=>'基础模块','name'=>'base'],
        ['title'=>'示例模块','name'=>'demo'],
    ],
]
```

## definitions
- 类型: string
- 默认值: app\common\controller\Definitions

指定公共注释定义的控制器地址



## controllers
- 类型: Array
- 默认值: undefined

列出需要生成接口文档的控制器，如不配置则自动根据 `route.controller_layer` 自动生成
```php
// config/apidoc.php
<?php
return [
    //生成文档的控制器
    'controllers' => [
        'api\\controller\\ApiTest',
        'api\\controller\\User',
        ...
    ],
]
```

## filter_controllers
- 类型: Array
- 默认值: undefined

如不配置`controllers`时有效，在自动生成控制器列表时配置不解析的控制器
```php
// config/apidoc.php
<?php
return [
    //生成文档的控制器
    'controllers' => [
        'api\\controller\\ApiTest',
        'api\\controller\\User',
        ...
    ],
]
```


## cache
- 类型: Array

缓存配置，开启后需手动更新接口参数，关闭则每次刷新重新生成接口数据

### cache.enable
- 类型: boolean
- 默认值:false

是否开启缓存

### cache.path
- 类型: string
- 默认值:'../runtime/apidoc/'

缓存文件路径


### cache.reload
- 类型: boolean
- 默认值:true

是否显示更新缓存按钮

### cache.max
- 类型: number
- 默认值:5

最大缓存数量



## auth
- 类型: Array

进入接口问页面的权限认证配置

### auth.enable
- 类型: boolean
- 默认值:false

是否启用权限认证，启用则需登录

### auth.password
- 类型: string
- 默认值:123456

进入接口文档页面的登录密码

### auth.secret_key
- 类型: string
- 默认值:apidoc#hg_code

密码加密的盐，请务必更改


## global_auth_key
- 类型: string
- 默认值: Authorization

设置全局Authorize时请求头headers携带的key，对应token的key


## responses
- 类型: string
- 默认值: 如下示例

统一的请求响应体，此配置只做展示用

```php
// config/apidoc.php
<?php
return [
    // 统一的请求响应体
    'responses'=>'{
    "code":"状态码",
    "message":"操作描述",
    "data":"业务数据",
    "timestamp":"响应时间戳"
}',
]
```


## filter_method
- 类型: array
- 默认值: 如下示例

指定公共注释定义的控制器地址

```php
// config/apidoc.php
<?php
return [
    // 过滤、不解析的方法名称
    'filter_method'=>[
        '_empty',
        '_initialize',
        '__construct',
        '__destruct',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__cal',
    ]
]
```

## docs
- 类型: array
- 默认值: undefined

Markdown文档配置

### docs.menu_title
- 类型: string
- 默认值: 开发文档

文档菜单主标题

### docs.menus
- 类型: array
- 默认值: undefined

生成Markdown文档菜单的配置

menu数组参数
|参数名|说明|
|-|-|
|title|文档标题|
|path|md文件地址，可使用 `{:version}` 做多版本区分|

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
> 