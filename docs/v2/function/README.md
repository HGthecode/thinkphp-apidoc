# 功能使用

## 多版本
::: warning 警告
 `v2.1.0`版本已删除`versions` 的配置项，请改为用`apps`配置[多应用/多版本](/v2/config/function.html#多应用-多版本)
:::


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


## 多应用/多版本
> \>=2.1.0版本

由于在各种项目开发中，有多种情况，如`单应用多版本`、`多应用无版本`、`多应用多版本`等开发场景与项目目录，所以将多应用/多版本统一在`apps`中配置实现。

#### 举例一个多应用多版本的实现：
假设一个admin应用无版本，demo应用有多个版本，其项目项目目录如下
```sh
app
 |—— admin
    |—— controller
       |—— Index.php
       ...
    |—— route
    ...
 |—— demo
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
在配置文件`/config/apidoc.php`中的 apps 参数中配置如下
```php
'apps' => [
    ['title'=>'后台管理','path'=>'app\admin\controller','folder'=>'admin'],
    [
        'title'=>'演示示例',
        'folder'=>'demo',
        'items'=>[
            ['title'=>'V1.0','path'=>'app\demo\controller\v1','folder'=>'v1'],
            ['title'=>'V2.0','path'=>'app\demo\controller\v2','folder'=>'v2']
        ]
    ],
],
```
得到如下效果
<img class="img-view" :src="$withBase('/images/v2/apidoc-demo-apps.png')" style="width:100%;" alt="apidoc-demo-apps">






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

配置文件`/config/apidoc.php`中的`cache`设置为如下：
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


## 快速生成CRUD

::: warning 注意
在`DEBUG`模式下可用，确保站点目录有777权限
:::

快速生成CRUD功能由 系统配置+模板+可视化页面配置来实现。为了灵活适应各种项目结构与实现方式，需做好系统配置与模板编写。

下面将举例一个多应用多版本的实现：
> 假设一个admin应用无版本，demo应用有多个版本，其项目项目目录如下
```sh
app
 |—— admin
    |—— controller
       |—— Index.php
       ...
    |—— service
    |—— validate
    ...
 |—— demo
    |—— controller
        |—— v1
            BaseDemo.php
            CrudDemo.php
            ...
        |—— v2
            BaseDemo.php
            CrudDemo.php
            ...
    |—— service
    |—— validate
 |—— model
 ...
```
### 1、系统配置

配置文件`/config/apidoc.php`中，根据你的项目结构设置[crud参数](/v2/config/#crud)
> 多应用/多版本需先配置 [apps](/v2/config/#apps) 

```php
// /config/apidoc.php

'crud'=>[
    // 生成控制器配置
    'controller'=>[
        'path'=>'app\${app[0].folder}\controller\${app[1].folder}',
        'template'=>'../template/controller',
    ],

    // 自定义的文件生成配置，你也可以像这样添加更多配置项，来生成你所需的文件。当然你的项目如果很简单，没有逻辑层删除此项配置也是可以的
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

通过如上配置，我们就可以看到页面上（搜索框后面）有一个 + 号按钮，点击弹出可视化配置窗口，如下

<img class="img-view" :src="$withBase('/images/v2/apidoc-config_crud.png')" style="width:100%;" alt="apidoc-config_crud">


### 2、编写模板
在项目跟目录创建 `template`文件夹，并创建配置中的模板文件 `controller.txt`、`service.txt`、`model.txt`、`validate.txt`、`route_admn.txt`、`route_demo.txt`。

模板示例可参考 [模板文件示例]() ，这里就不贴出来了，这里说说如何编写模板，相信你很快就能上手。

::: tip 要领
1、首先自己在项目中实现一个标准通用的Crud。

2、然后把这些文件内容拷贝在对应的`.txt`模板文件中。

3、用下表变量替换模板中的参数。
:::

#### 模板变量
在模板中可以使用如下变量，使用方法`${变量名}`

|变量名|示例值|说明|
|-|-|-|
|title|测试CRUD|页面填入的控制器标题|
|controller.class_name|TestCrud|页面填入的控制器文件名|
|controller.namespace|app\admin\controller|控制器命名空间|
|controller.use_alias|TestCrudController|控制器别名|
|controller.use_path|app\admin\controller\TestCrud|控制器use地址|
|list.field|id,name,age,sex|页面勾选的列表显示字段|
|list.withoutField|update_time,delete_time|列表显示排除的字段|
|detail.field|id,name,age,sex|页面勾选的明细显示字段|
|detail.withoutField|update_time,delete_time|明细显示排除的字段|
|add.field|name,age,sex|页面勾选的新增字段|
|add.withoutField|id,update_time,delete_time|新增提交排除的字段|
|edit.field|id,name,age,sex|页面勾选的编辑显示字段|
|edit.withoutField|update_time,delete_time|编辑显示排除的字段|
|query.annotation|@Apidoc\Param("name",type="string",desc="姓名")|根据页面勾选查询的字段生成的注释|
|query.where|$where=\[\]; if(!empty($param\['name'\])){ $where\[\] = \['name','=',$param\['name'\]\]; }|根据页面勾选的查询字段生成的查询条件|
|api_class_name|testCrud|api的url所用的控制器名称（其实就是小写开头的控制器名）|
|api_group|app\admin\controller\TestCrud|控制器分组所选的分组name|
|api_url|/admin/testCrud|控制器api访问的url|
|main_key.field|id|页面配置的数据表主键的字段名|
|main_key.type|int|页面配置的数据表主键的字段类型|
|main_key.desc|唯一id|页面配置的数据表主键的字段说明注释|
|model.class_name|TestCrud|页面填入的模型文件名|
|model.namespace|app\model|模型命名空间|
|model.use_alias|TestCrudModel|模型别名|
|model.use_path|app\model\TestCrud|模型use地址|
|validate.class_name|TestCrud|页面填入的验证器文件名|
|validate.namespace|app\admin\validate|验证器命名空间|
|validate.use_alias|TestCrudValidate|验证器别名|
|validate.use_path|app\admin\validate\TestCrud|验证器use地址|
|validate.rule|[ 'id' => 'require','name'=>'require']|页面字段配置验证的验证规则|
|validate.message|['id' =>'id不可为空','name'=>'name不可为空']|验证规则的提示内容|
|validate.scene.add|\["name","sex","age"\]|新增场景验证的字段|
|validate.scene.edit|\["id","name","sex","age"\]|编辑场景验证的字段|
|validate.scene.delete|\["id"\]|删除场景验证的字段|
|service.class_name|TestCrud|页面填入的逻辑层（自定义的）文件名|
|service.namespace|app\admin\service|逻辑层（自定义的）命名空间|
|service.use_alias|TestCrudservice|逻辑层（自定义的）别名|
|service.use_path|app\admin\service\TestCrud|逻辑层（自定义的）use地址|

#### 自定义文件
如果你的项目需要更多层文件来实现Crud，你只需要在配置文件`config/apidoc.php`的`crud`配置项中加入你的配置，如下

```php
// /config/apidoc.php

'crud'=>[
    // 自定义的文件生成配置，你也可以像这样添加更多配置项，来生成你所需的文件
    'myTest'=>[
        'path'=>'app\${app[0].folder}\myTest',
        'template'=>'../template/myTest',
    ],
    //...
]
```
加入配置后，页面将会多出一个myTest的文件名输入项，并在创建Crud时根据此配置的模板生成文件。同时模板变量里也会多出以下几个变量

|变量名|示例值|说明|
|-|-|-|
|myTest.class_name|TestCrud|页面填入的自定义的文件名|
|myTest.namespace|app\admin\myTest|自定义文件的命名空间|
|myTest.use_alias|TestCrudmyTest|自定义文件的别名|
|myTest.use_path|app\admin\myTest\TestCrud|自定义文件的use地址|



