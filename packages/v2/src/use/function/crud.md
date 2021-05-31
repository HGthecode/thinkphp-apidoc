
# 快速生成CRUD

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
## 1、系统配置

配置文件`/config/apidoc.php`中，根据你的项目结构设置[crud参数](/config/#crud)
> 多应用/多版本需先配置 [apps](/config/#apps) 

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

![apidoc-config_crud](/thinkphp-apidoc/images/apidoc-config_crud.png "apidoc-config_crud")

## 2、编写模板
在项目跟目录创建 `template`文件夹，并创建配置中的模板文件 `controller.txt`、`service.txt`、`model.txt`、`validate.txt`、`route_admn.txt`、`route_demo.txt`。

模板示例可参考 [模板文件示例](https://github.com/HGthecode/thinkphp-apidoc-demo/tree/multiApp/template) ，这里就不贴出来了，这里说说如何编写模板，相信你很快就能上手。

::: tip 要领
1、首先自己在项目中实现一个标准通用的Crud。

2、然后把这些文件内容拷贝在对应的`.txt`模板文件中。

3、用下表变量替换模板中的参数。
:::

### 模板变量
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
|model.table_name| test_crud |表明（不含前缀）|
|model.table_prefix|""|表前缀|
|model.file_name| TestCrud | 文件名称|
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
|service.file_name|TestCrud|文件名|

### 自定义文件
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

