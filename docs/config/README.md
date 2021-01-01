---
sidebarDepth: 2
---
# 配置参数
::: tip
安装插件后会在 /config/ 目录下生成一个 apidoc.php 的配置文件，以下为该文件可配置的参数说明，
默认配置文件请查看 [apidoc.php](https://github.com/HGthecode/thinkphp-apidoc/blob/master/config/apidoc.php)
:::

## title
- 类型: string
- 默认值: APi接口文档

页面的标题，显示在左上角与封面页

## desc
- 类型: string

文档封面处显示

## copyright
- 类型: string
- 默认值: Powered By HG

页面的标题，版权申明，显示在封面页

## controllers
- 类型: Array
- 默认值: undefined

列出需要生成接口文档的控制器
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

## with_cache
- 类型: boolean
- 默认值: false

是否开启缓存，开启后，如存在缓存数据优先取缓存数据，开启后需手动更新接口参数，关闭则每次刷新重新生成接口数据


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


## global_auth_key
- 类型: string
- 默认值: Authorization

设置全局Authorize时请求头headers携带的key，对应token的key

## auth
- 类型: Array

进入接口问页面的权限认证配置


### auth.with_auth
- 类型: boolean
- 默认值:false

是否启用权限认证，启用则需登录

### auth.auth_password
- 类型: string
- 默认值:123456

进入接口文档页面的登录密码


## definitions
- 类型: string
- 默认值: app\common\controller\Definitions

指定公共注释定义的控制器地址

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

