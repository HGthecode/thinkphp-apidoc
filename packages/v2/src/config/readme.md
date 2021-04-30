---
icon: config
category: 配置
# sidebarDepth: 2
# sidebar: auto
---

# 配置参数
::: tip
安装插件后会在/config/目录生成一个apidoc.php的配置文件，以下为该文件可配置的参数说明

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

## default_method
- 类型: string
- 默认值: GET

默认的请求类型，编写接口注解时，不配置Method参数时，为该配置的默认类型

## default_author
- 类型: string
- 默认值: GET

默认的作者名称，编写接口注解时，不配置Author时，为该配置的默认作者

## versions <MyBadge text="已弃用" type="error"/>
- 类型: array
- 默认值: undefined

设置API文档的版本
> `v2.1.0`已删除，改为用`apps`配置
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

## apps

- 类型: array
- 默认值: undefined
- 支持版本：>=2.1.0

多应用/多版本管理配置

::: warning 注意
修改了该配置，必须去掉页面url?（问号）后面的所有参数，重新访问
:::

```php
// config/apidoc.php

'apps' => [
    ['title'=>'后台管理','path'=>'app\admin\controller','folder'=>'admin'],
    [
        'title'=>'演示示例',
        'path'=>'app\demo\controller',
        'folder'=>'demo',
        'items'=>[
            ['title'=>'V1.0','path'=>'app\demo\controller\v1','folder'=>'v1'],
            ['title'=>'V2.0','path'=>'app\demo\controller\v2','folder'=>'v2']
        ]
    ],
],
```
apps配置数组的参数说明
|参数名|类型|说明|
|-|-|-|
|title|string|应用/版本的名称|
|path|string|应用/版本的目录（控制器命名空间）|
|folder|string|应用/版本的文件夹名称|
|password|string|应用/版本的访问密码|
|items|array|多层应用/版本配置|


## groups  
- 类型: array
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
- 类型: array
- 默认值: undefined

列出需要生成接口文档的控制器，如不配置则自动根据 `apps` 配置的path自动生成
```php
// config/apidoc.php
<?php
return [
    //生成文档的控制器
    'controllers' => [
        'app\\api\\controller\\ApiTest',
        'app\\api\\controller\\User',
        ...
    ],
]
```

## filter_controllers
- 类型: array
- 默认值: undefined

如不配置`controllers`时有效，在自动生成控制器列表时配置不解析的控制器
```php
// config/apidoc.php
<?php
return [
    //生成文档的控制器
    'controllers' => [
        'app\\api\\controller\\ApiTest',
        'app\\api\\controller\\User',
        ...
    ],
]
```


## cache
- 类型: array

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
> `v2.1.0`已删除，改为用TP的 `APP_DEBUG`控制

### cache.max
- 类型: number
- 默认值:5

最大缓存数量



## auth
- 类型: array

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


## global\_auth\_key <MyBadge text="已弃用" type="error"/>
- 类型: string
- 默认值: Authorization

> `v2.4.0`版本已弃用

设置全局Authorize时请求头headers携带的key，对应token的key


## headers
- 类型: array
- 默认值: undefined
- 支持版本：>=2.4.0

全局请求头参数，配置后，所有接口请求头参数都统一加上这些参数，如接口单独定义了将会覆盖配置中的全局参数
```php
// config/apidoc.php

// 全局的请求头参数
'headers'=>[
    ['name'=>'token','type'=>'string','require'=>true,'desc'=>'登录票据'],
    ['name'=>'shopid','type'=>'int','desc'=>'店铺id'],
],
```

## parameters
- 类型: array
- 默认值: undefined
- 支持版本：>=2.4.0

全局请求参数，配置后，所有接口请求参数都统一加上这些参数，如接口单独定义了将会覆盖配置中的全局参数
```php
// config/apidoc.php

// 全局的请求参数
'parameters'=>[
    ['name'=>'code','type'=>'string','desc'=>'全局code'],
],
```


## responses
- 类型: string/object/array
- 默认值: 如下示例

统一的请求响应体，当配置为字符串时，只在接口详情页面`响应结果Responses`右侧的问号处做展示用
```php
// config/apidoc.php

// 统一的请求响应体
'responses'=>'{
    "code":"状态码",
    "message":"操作描述",
    "data":"业务数据",
    "timestamp":"响应时间戳"
}',

//  >= v2.2.1版本
'responses'=>[
    'show_responses'=>true,
    'data'=>[
        ['name'=>'code','desc'=>'状态码','type'=>'int'],
        ['name'=>'message','desc'=>'操作描述','type'=>'string'],
        ['name'=>'data','desc'=>'业务数据','main'=>true,'type'=>'object'],
    ]
],

//  >= v2.4.0版本（推荐）
'responses'=>[
    ['name'=>'code','desc'=>'状态码','type'=>'int'],
    ['name'=>'message','desc'=>'操作描述','type'=>'string'],
    ['name'=>'data','desc'=>'业务数据','main'=>true,'type'=>'object'],
],

```

### responses.show_responses
- 类型: boolean
- 默认值: false

> `v2.4.0`版本已弃用，改为以上示例 v2.4.0版本的使用方式

是否将统一响应体数据显示在响应结果中


### responses.data
- 类型: array
- 默认值: undefined

> `v2.4.0`版本已弃用，改为以上示例 v2.4.0版本的使用方式

统一响应体的数据结构配置，如配置`responses.show_responses`为true时，响应体数据中必须有一个指定`main`为true，以将接口数据挂载到该字段下




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
|path|md文件地址，可使用 `${app[N].folder}` 做应用/版本区分，具体用法见[path可用变量说明](/config/#path可用变量说明)|

```php
// config/apidoc.php
<?php
return [
    // markdown 文档
    'docs' => [
        'menu_title' => '开发文档',
        'menus'      => [
            ['title'=>'md语法示例','path'=>'docs/Use'],
            [
                'title'=>'HTTP响应编码',
                'items'=>[
                    ['title'=>'status错误码说明','path'=>'docs/${app[0].folder}/HttpStatus'],
                    ['title'=>'code错误码说明','path'=>'docs/${app[0].folder}/HttpCode_${app[1].folder}'],
                ],
            ]
        ]
    ]
]
```

### path可用变量说明
变量写法`${app[N].folder}`其中的`N`表示`apps`中配置的层级：

比如配置为如下
```php
'apps' => [
    ['title'=>'后台管理','path'=>'app\admin\controller','folder'=>'admin'],
    [
        'title'=>'演示示例',
        'path'=>'app\demo\controller',
        'folder'=>'demo',
        'items'=>[
            ['title'=>'V1.0','path'=>'app\demo\controller\v1','folder'=>'v1'],
            ['title'=>'V2.0','path'=>'app\demo\controller\v2','folder'=>'v2']
        ]
    ],
],
'docs'=>[
    'menu_title' => '开发文档',
    'menus'      => [
        ['title'=>'Http状态码','path'=>'docs/${app[0].folder}/HttpCode_${app[1].folder}'],
    ]
]
```
1、当应用/版本选为`后台管理`的应用时，此时`${app[0].folder}`就等于`admin` 由于该应用配置无子级`items`此时的`${app[1].folder}`也就为空。最终文件地址为`dosc/admin/HttpCode_.md`。

2、当应用/版本选为`演示示例-V1.0`时，此时`${app[0].folder}`就等于`demo` 由于该应用配置存在子级（多个版本）`items`此时的`${app[1].folder}`也就为`v1`。最终文件地址为`dosc/admin/HttpCode_v1.txt`。


## crud
- 类型: object
- 默认值: undefined
- 支持版本：>=2.1.0

可视化一键生成Crud的配置

### crud.controller 的参数

|参数名|类型|说明|
|-|-|-|
|path|string|生成控制器文件到此目录，可使用`${app[N].folder}`变量指定当前选中的`apps`配置中的参数，具体可查看说明 |
|template|string|生成控制器的模板文件地址。`../`为项目根目录；模板文件必须为`.txt`；可使用`${app[N].folder}`变量指定当前选中的`apps`配置中的参数，具体可查看[path、template可用变量说明](/config/#path、template可用变量说明)|

### crud.route 的参数

|参数名|类型|说明|
|-|-|-|
|path|string|生成路由到此文件，注意：这里指定的是路由文件，不是目录 |
|template|string|生成路由的模板文件地址。[同crud.contreller.template参数](/config/#crud-controller-的参数)|

### crud.model 的参数

|参数名|类型|说明|
|-|-|-|
|path|string|生成模型文件到此目录 |
|template|string|模型生成的模板地址。[同crud.contreller.template参数](/config/#crud-controller-的参数)|
|default_fields|array|创建数据表窗口，默认填入的字段方便快速填写，具体可参考下方示例|
|fields_types|array|创建数据表窗口，可选的字段类型|

### crud.validate 的参数

|参数名|类型|说明|
|-|-|-|
|path|string|生成验证器文件到此目录 |
|template|string|验证器生成的模板地址。[同crud.contreller.template参数](/config/#crud-controller-的参数)|
|rules|array|可选的验证规则|

#### crud.validate.rules的参数
|参数名|类型|说明|
|-|-|-|
|name|string|验证规则的名称 |
|rule|string|验证规则。具体可查阅TP文档[TP6验证规则](https://www.kancloud.cn/manual/thinkphp6_0/1037625)|
|message|array/string |提示文本，可使用`${变量名}`来获取当前字段的参数，可配置的参数同`crud.model.default_fields`数组对象中的参数|


### crud[自定义] 的参数

|参数名|类型|说明|
|-|-|-|
|path|string|生成自定义文件到此目录 |
|template|string|自定义生成的模板地址。[同crud.contreller.template参数](/config/#crud-controller-的参数)|


### path、template可用变量说明
变量写法`${app[N].folder}`其中的`N`表示`apps`中配置的层级：

比如配置为如下
```php
'apps' => [
    ['title'=>'后台管理','path'=>'app\admin\controller','folder'=>'admin'],
    [
        'title'=>'演示示例',
        'path'=>'app\demo\controller',
        'folder'=>'demo',
        'items'=>[
            ['title'=>'V1.0','path'=>'app\demo\controller\v1','folder'=>'v1'],
            ['title'=>'V2.0','path'=>'app\demo\controller\v2','folder'=>'v2']
        ]
    ],
],
'crud'=>[
    // 生成控制器配置
    'controller'=>[
        'path'=>'app\${app[0].folder}\controller\${app[1].folder}',
        'template'=>'../template/${app[0].folder}/controller',
    ],
    // ...
]
```
1、当创建Crud时所选为`后台管理`的应用时，此时`${app[0].folder}`就等于`admin` 由于该应用配置无子级`items`此时的`${app[1].folder}`也就为空。最终将采用`template/admin/controller.txt`的模板文件来生成的controller文件目录为 `app\admin\controller`。

2、当创建Crud时所选为`演示示例-V1.0`的应用/版本时，此时`${app[0].folder}`就等于`demo` 由于该应用配置存在子级（多个版本）`items`此时的`${app[1].folder}`也就为`v1`。最终将采用`template/demo/controller.txt`的模板文件来生成的controller文件目录为 `app\demo\controller\v1`。



### crud配置示例
```php
'crud'=>[
    // 生成控制器配置
    'controller'=>[
        'path'=>'app\${app[0].folder}\controller\${app[1].folder}',
        'template'=>'../template/controller',
    ],

    // 自定义的文件生成配置，你也可以像这样添加更多配置项，来生成你所需的文件
    'service'=>[
        'path'=>'app\${app[0].folder}\services',
        'template'=>'../template/service',
    ],

    // 生成模型配置
    'model'=>[
        'path'=>'app\model',
        'template'=>'../template/model',
        'default_fields'=>[
            [
                'field'=> 'id',
                'desc'=> '唯一id',
                'type'=> 'int',
                'length'=> 11,
                'default'=> '',
                'not_null'=> true,
                'main_key'=> true,
                'incremental'=> true,
                'validate'=>'',
                'query'=> false,
                'list'=> true,
                'detail'=> true,
                'add'=> false,
                'edit'=> true
            ],
        ],
        'fields_types'=>[
            "int",
            "tinyint",
            "float",
            "decimal",
            "char",
            "varchar",
            "text",
            "point",
        ]
    ],
    // 生成验证器文件配置
    'validate'=>[
        'path'=>'app\${app[0].folder}\validate',
        'template'=>'../template/validate',
        'rules'=>[
            ['name'=>'必填','rule'=>'require','message'=>'缺少必要参数${field}'],
            ['name'=>'数字','rule'=>'number','message'=>['${field}字段类型为数字']],
            ['name'=>'年龄','rule'=>'number|between:1,120','message'=>['${field}.number'=>'${field}${desc}字段类型为数字','${field}.between'=>'${field}只能在1-120之间']]
        ]
    ],
    // 生成路由
    'route'=>[
        'path'=>'${app[0].folder}\route\${app[0].folder}.php',
        'template'=>'../template/route_${app[0].folder}',
    ]
]

```
