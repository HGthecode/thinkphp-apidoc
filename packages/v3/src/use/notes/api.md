# 接口注释

控制器中的每一个符合注释规则的方法都会被解析成一个API接口

## 基础注释
先来体验一个最基本的注释，所得到的结果

我们在控制器中加入如下方法，如下

```php
<?php

use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("基础示例")
 */
class ApiDocTest
{ 
    /**
     * @Apidoc\Title("基础的注释方法")
     * @Apidoc\Desc("最基础的接口注释写法")
     * @Apidoc\Method("GET")
     * @Apidoc\Author("HG-CODE")
     * @Apidoc\Tag("测试")
     * @Apidoc\Param("username", type="abc",require=true, desc="用户名")
     * @Apidoc\Param("password", type="string",require=true, desc="密码")
     * @Apidoc\Param("phone", type="string",require=true, desc="手机号")
     * @Apidoc\Param("sex", type="int",default="1",desc="性别" )
     * @Apidoc\Returned("id", type="int", desc="用户id")
     */
    public function base(){
        //...
    }
  
}
```

以上注释，我们得到的效果如下

![apidoc-api-base-demo](/thinkphp-apidoc/images/apidoc-api-base-demo.png "apidoc-api-base-demo")


## 通用注释

通过定义通用的公共注释参数来实现 可复用性，避免每个接口都定义一大堆同样的参数

### 1、增加配置

首先，在配置文件 config/apidoc.php 配置文件中，指定一个控制器为定义公共注释的控制器

```php
// config/apidoc.php
// 指定公共注释定义的文件地址
'definitions'=>"app\controller\Definitions",
```

### 2、定义通用注释

添加一些通用的方法及注释，（定义param 与returned 参数与接口注释书写规则一致）

```php
<?php
namespace app\controller;

use hg\apidoc\annotation\Param;
use hg\apidoc\annotation\Returned;
use hg\apidoc\annotation\Header;


class Definitions
{
    /**
     * 获取分页数据列表的参数
     * @Param("pageIndex",type="int",require=true,default="0",desc="查询页数")
     * @Param("pageSize",type="int",require=true,default="20",desc="查询条数")
     * @Returned("total", type="int", desc="总条数")
     */
    public function pagingParam(){}
  
    /**
     * 返回字典数据
     * @Returned("id",type="int",desc="唯一id")
     * @Returned("name",type="string",desc="字典名")
     * @Returned("value",type="string",desc="字典值")
     */
    public function dictionary(){}

    /**
     * @Header("token",type="string",require=true,desc="身份票据")
     * @Header("shopid",type="string",require=true,desc="店铺id")
     */
    public function auth(){}
    
}
```

### 3、使用定义

在接口注释中的 param 与 retruned 可通过 ref="XXX" 来指定引入的 通用注释

```php
<?php
namespace app\controller;

use hg\apidoc\annotation as Apidoc;

class ApiDocTest
{ 
    /**
     * @Apidoc\Title("引入通用注释")
     * @Apidoc\Desc("引入配置中definitions的通用注解控制器中所定义的通用参数")
     * @Apidoc\Url("/admin/refDemo/definitions")
     * @Apidoc\Author("HG")
     * @Apidoc\Method("GET")
     * @Apidoc\Header( ref="auth")
     * @Apidoc\Param( ref="pagingParam")
     * @Apidoc\Param("page",type="object", ref="pagingParam",desc="分页参数")
     * @Apidoc\Returned("list", type="array",ref="dictionary", desc="字典列表")
     */
    public function definitions(){
        //...
    }
}
```
:::tip 以上param用了两种方式引入，分别是参数指定 字段名 与 type ，与不指定字段名
- 指定字段名：会将引入的参数在该字段属性下
- 不指定字段名：直接引入所有参数
:::

效果如下

![apidoc-api-dictionary-demo](/thinkphp-apidoc/images/apidoc-api-dictionary-demo.png "apidoc-api-dictionary-demo")

## 逻辑层注释

在实际开发中，控制器只对参数做基础校验等处理，实际的业务逻辑处理通常会分层给逻辑层来处理（我这里把业务逻辑层叫service，您也可以根据自己开发来定义 业务逻辑层），我们可直接引入业务逻辑层的注释来实现接口参数的定义

### 增加业务逻辑层

1、在项目 app 目录下（或应用/模块目录）新建 services 文件夹（也可以叫别的）

2、在此文件夹下新建一个ApiDoc.php文件，内容如下：

```php
<?php
namespace app\services;

use hg\apidoc\annotation as Apidoc;

class ApiDoc
{

     /**
     * @Apidoc\Param("sex", type="int",require=true,desc="性别")
     * @Apidoc\Param("age", type="int",require=true,desc="年龄")
     * @Apidoc\Param("id", type="int",require=true,desc="唯一id")
     * @Apidoc\Returned("id", type="int",desc="唯一id")
     * @Apidoc\Returned("name", type="string",desc="姓名")
     * @Apidoc\Returned("phone", type="string",desc="电话")
     */
    public function getUserInfo(){}

    
}
```

### 引用逻辑层注释

在控制器的接口注释中的 param 与 retruned 可通过 ref="app\services\ApiDoc\getUserInfo"来指定引入逻辑层的注释

```php
<?php
namespace app\controller;

use hg\apidoc\annotation as Apidoc;

class ApiDocTest
{ 
    /**
     * @Apidoc\Title("引入逻辑层注释")
     * @Apidoc\Desc("引入业务逻辑层（其它分成）的注解参数")
     * @Apidoc\Url("/admin/refDemo/service")
     * @Apidoc\Method("GET")
     * @Apidoc\Param(ref="app\admin\services\ApiDoc\getUserInfo")
     * @Apidoc\Returned(ref="\app\admin\services\ApiDoc\info")
     */
    public function service(){
       //...
    }
}
```


效果如下

![apidoc-api-server-demo](/thinkphp-apidoc/images/apidoc-api-server-demo.png "apidoc-api-server-demo")



## 模型注释

接口参数都与数据表息息相关，很多接口参数均由数据表字段而来。我们可以直接引入指定模型的数据表字段来生成参数说明，省去了一大堆接口注释与维护工作。

### 给数据表字段添加注释

建议为数据表字段添加注释，即让数据表字段可读性更高，也让文档可读性更高。
我们直接在数据表给相应字段添加注释，如下SQL供参考

```php
CREATE TABLE `user` (↵  
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` varchar(64) NOT NULL COMMENT '用户名',
  `nickname` varchar(64) DEFAULT NULL COMMENT '昵称',
  `password` char(64) NOT NULL COMMENT '登录密码',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `regip` bigint(11) DEFAULT NULL COMMENT '注册IP',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `state` tinyint(1) DEFAULT '1' COMMENT '状态',
  `phone` char(32) DEFAULT NULL COMMENT '联系电话',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `sex` tinyint(1) unsigned DEFAULT '1' COMMENT '性别',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  `role` varchar(64) DEFAULT NULL COMMENT '角色',
  `name` varchar(64) DEFAULT NULL COMMENT '姓名',
PRIMARY KEY (`id`)↵) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8"
```

### 模型方法的注释

可为引入的数据模型方法添加相应注释来实现 field（返回指定字段）、withoutField（排除指定字段）、addField（添加指定字段）

|参数|说明|书写规范|
|-|-|-|
|field|返回指定字段|英文格式逗号 , 分开指定的字段|
|withoutField|排除指定字段|英文格式逗号 , 分开指定的字段|
|addField|添加指定字段|可定义多个，每行为一个参数，也可如下示例嵌套Param使用来定义复杂层级的数据结构|
|   \|—  |参数的字段名|如：@addField("name")|
|   \|— type|字段类型|
|   \|— require|是否必填| |
|   \|— default|默认值| |
|   \|— desc|字段说明文字||

```php
<?php
namespace app\model;

use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\Param;

class User extends BaseModel
{

     /**
     * @Field("id,username,nickname,role")
     * @AddField("openid",type="string",default="abc",desc="微信openid")
     * @AddField("senkey",type="string",default="key",desc="微信key")
     * @AddField("role",type="array",desc="重写role，该定义会覆盖数据表中的字段描述",
     *     @Param ("name",type="string",desc="名称"),
     *     @Param ("id",type="string",desc="id"),
     * )
     */
    public function getInfo($id){
        $res = $this->get($id);
        return $res;
    }
}
```

### 控制器引用模型注释
```php
<?php
namespace app\controller;

use hg\apidoc\annotation as Apidoc;

class ApiDocTest
{ 
    /**
     * @Apidoc\Title("引入模型注释")
     * @Apidoc\Desc("param参数为直接引用模型参数；return则是引用逻辑层，通过逻辑层引用模型参数")
     * @Apidoc\Author("HG")
     * @Apidoc\Url("/v1/baseDemo/model")
     * @Apidoc\Method("GET")
     * @Apidoc\Param(ref="app\model\User\getInfo")
     * @Apidoc\Returned("userList",type="array",ref="app\model\User\getInfo")
     */
    public function model(){
       //...
    }
}
```


![apidoc-api-model-demo](/thinkphp-apidoc/images/apidoc-api-model-demo.png "apidoc-api-model-demo")



## 复杂注释

> \>=2.3.0 版本

虽然apidoc拥有强大的ref引用能力，但某些场景我们需要在一个方法内完成多层数据结构的注解，此时我们可以将`Param`,`Returned`做嵌套使用即可

```php
<?php
namespace app\controller;

use hg\apidoc\annotation as Apidoc;

class ApiDocTest
{ 
    /**
     * 直接定义多层结构的参数
     * @Apidoc\Desc("仅在一个方法注释中定义多层数据结构的参数")
     * @Apidoc\Url("/admin/baseDemo/completeParams")
     * @Apidoc\Param("info",type="object",desc="信息",
     *     @Apidoc\Param ("name",type="string",desc="姓名"),
     *     @Apidoc\Param ("sex",type="string",desc="性别"),
     *     @Apidoc\Param ("group",type="object",desc="所属组",
     *          @Apidoc\Param ("group_name",type="string",desc="组名"),
     *          @Apidoc\Param ("group_id",type="int",desc="组id"),
     *          @Apidoc\Param ("data",type="object",ref="app\admin\services\ApiDoc\getUserList",desc="这里也可以用ref")
     *     )
     * )
     * @Apidoc\Returned("info",type="object",desc="信息",
     *     @Apidoc\Returned ("name",type="string",desc="姓名"),
     *     @Apidoc\Returned ("sex",type="string",desc="性别"),
     *     @Apidoc\Returned ("group",type="object",desc="所属组",
     *          @Apidoc\Returned ("group_name",type="string",desc="组名"),
     *          @Apidoc\Returned ("group_id",type="int",desc="组id"),
     *     )
     * )
     */
    public function test(){
       //...
    }
}
```

![apidoc-api-complete-demo.png](/thinkphp-apidoc/images/apidoc-api-complete-demo.png "apidoc-api-complete-demo.png")



## 参数说明

::: warning 注意
- 每个参数以 @+参数名("参数值",子参数名="子参数值",...)
- 参数名首字母大写，避免有些环境不能正确解析小写首字母
- 子参数的值需用"双引号"包起来 
:::

|参数名|参数值|说明|书写规范|
|-|-|-|-|
|Title| |	接口名称 |	任意字符，也可如以下[特殊参数](#特殊参数)直接写在注释前面 |	
|Desc|	|接口描述 |	任意字符 |	
|Md|	|Markdown描述，子参数`ref`引用一个md文件内容 |	Markdown语法字符 |	
|Author|	|作者 |	任意字符,默认配置文件的`apidoc.default_author` |	
|Url|	|真实的接口URL，不配置时会根据控制器目录自动生成 |	任意字符 |	
|Method|	`GET` `POST` `PUT` `DELETE` |请求类型,默认配置文件的`apidoc.default_method`,`>=v2.5.0`版本支持配置多个(用,隔开) |	 |	
|Tag|	|接口Tag标签 |	多个标签用,（逗号）空格隔开 |	
|Header| 具体查看 [Header、Param、Returned的参数](/use/#header、param、return-的参数)	|请求头Headers参数 |	可定义多个|	
|Param | 具体查看 [Header、Param、Returned的参数](/use/#header、param、return-的参数)	|请求参数 |	可定义多个 |	
|ParamType| `json` `formdata` `route`	|请求参数类型，默认json。为route时，接口调试参数将替换路由表达式中的变量传递 | |
|ParamMd | 	| 使用Markdown定义请求参数内容，可ref引用md文件 |	 |	
|Returned| 具体查看 [Header、Param、Returned的参数](/use/#header、param、return-的参数)	|响应结果 |	可定义多个 |	
|ReturnedMd | 	| 使用Markdown定义响应内容，可ref引用md文件 |	 |	
|Before| 具体查看 [功能使用-调试时的事件](/use/function/debugEvent/)	|调试时请求发起前执行的事件 |	可定义多个 |
|After| 具体查看 [功能使用-调试时的事件](/use/function/debugEvent/)	|调试时请求返回后执行的事件 |	可定义多个 |

> 如使用了官方的注解路由，如`@Route("hello/index", method="GET")`, 可不写 `Url` `Method`注解

### Header|Param|Returned参数

|参数名|说明|书写规范|
|-|-|-|
| |	参数的字段名 |	如：@Apidoc\Param("name")，如使用ref引入某个定义，可不配置参数值 |	
| type| 	字段类型 | `string` `int` `boolean` `array` `object` `tree` `file` `float` `date` `time` `datetime`	 |	
| require|	是否必填，`param`有效 |	 |	
| default|默认值 |  |	
| desc|	字段描述 |	 |	
| md|	Markdown描述内容 |	 |	
| mdRef|	引用md文件内容 | 如：`/docs/xxx.md#name`	 |	
| mock|	接口调试时生成该字段的值，支持的参数值请查看[mock语法]() | 	 |	
| ref|	引入定义的路径，可引入全局定义、服务层方法类、模型方法 |<div>如：@Apidoc\Param(ref="pagingParam")</div><div>或：@Apidoc\Param(ref="app\services\ApiDocTest\get")</div><div>或：@Apidoc\Param(ref="app\model\User\getList")</div>	 |	
| field|	配置了ref引入时有效，用来指定引入的字段 | 如：field="id,username,nickname"；则只会引入定义的 id,username字段	 |	
| withoutField|	配置了ref引入时有效，用来指定过滤掉的字段 | 如：withoutField:id,username；则引入模型除 id,username字段外的所有字段	 |	
| childrenField|	字段类型为`tree`时，给其定义子节点字段名 |	默认为 children |	
| childrenDesc|	字段类型为`tree`时，给其定义子节点字段名的备注|	 |
| childrenType| 字段类型为`array`时，为子参数定义类型，可选值有`string` `int` `boolean` `array` `object` |  |
| replaceGlobal| Returned有效，replaceGlobal="true"时，将该字段覆盖配置的`responses`统一响应体参数 |  |



## 特殊参数

::: tip 说明
特殊参数以字符方式直接写到注释中，如下
:::

|参数名|说明|
|-|-|
|NotParse| 不需要解析的方法 |	
|NotHeaders| 不使用配置中的全局请求头参数 |	
|NotParameters| 不使用配置中的全局请求参数 |	
|NotResponses| 不使用统一响应体返回数据 |	
|NotDefaultAuthor| 不使用默认作者 |	
|NotDebug| 关闭接口调试 |	



```php
<?php
namespace app\controller;
use hg\apidoc\annotation as Apidoc;

/**
 * NotParse
 */
class ApiDocTest
{ 
   /**
     * NotParse
     * NotResponses
     */
    public function model(){
       //...
    }
}
```