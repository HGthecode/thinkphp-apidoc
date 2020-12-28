# 编写注释
::: tip
由于API接口文档根据解析代码中的注释生成，需按照一定的书写规则来生成
:::

## 书写规范
::: warning 书写参数时有如下几个规范
- 每个参数书写在一行 
- 每个参数以 @+参数名，用空格 与参数值隔开 如  * @参数名 参数值
:::

## 参数说明

|参数名|参数值|说明|书写规范|
|-|-|-|-|
|title| |	接口名称 |	任意字符 |	
|desc|	|接口说明 |	任意字符 |	
|author|	|作者 |	任意字符 |	
|url|	|真实的接口URL |	任意字符 |	
|method|	`GET` `POST` `PUT` `DELETE` |请求类型 |	 |	
|tag|	|接口Tag标签 |	多个标签用 空格隔开 |	
|header| 具体查看 [header的参数](/use/#header-的参数)	|请求头Headers参数 |	可定义多个，每个一行，每个属性用空格隔开 |	
|param | 具体查看 [param、return的参数](/use/#param、return-的参数)	|请求参数 |	可定义多个，每个一行，每个属性用空格隔开 |	
|return| 具体查看 [param、return的参数](/use/#param、return-的参数)	|响应结果 |	可定义多个，每个一行，每个属性用空格隔开 |	


### header 的参数

|参数名|说明|书写规范|
|-|-|-|
| name|	参数的字段名 |	如：name:Authorization |	
| require|	是否必填 |	如：require:1 为必填 |	
| default|	默认值 |	如：default:123 |	
| desc|字段说明 |	 |	


### param、return 的参数

|参数名|说明|书写规范|
|-|-|-|
| name|	参数的字段名 |	如：name:username，如直接使用ref引入某个定义，可不配置name |	
| type| 	字段类型 | `int` `string` `boolean` `object` `array` `tree`	 |	
| require|	是否必填 |	如：require:1 为必填 |	
| default|默认值 |	如：default:123 |	
| desc|	字段说明 |	 |	
| ref|	引入定义的路径，可引入全局定义、server方法类、模型方法 |<div>如：ref:definitions\pagingParam</div><div>或：ref:app\servers\ApiTest\get</div><div>或：ref:app\model\Apps\getList</div>	 |	
| field|	当ref配置为引入模型字段时，用field来指定引入的字段 | 如：field:id,username,nickname ；则只会引入模型的 id,username,nickname字段	 |	
| withoutField|	当ref配置为引入模型字段时，用withoutField来指定过滤掉的字段 | 如：withoutField:id,username,nickname；则引入模型除 id,username,nickname字段外的所有字段	 |	
| params|	字段类型为`object`或`array`，给其定义子节点参数 |	如：params:id int 1 唯一id,name:string 0 姓名 |	


## 控制器注释
1、接口文档将按照在配置文件`/config/apidoc.php`中配置的 controllers 控制器列表，来生成.

若你希望 某个控制器被解析，那么首先在配置项中加入该控制器，如下：
```php
// config/apidoc.php
// 将 app\controller\ApiTest.php 控制器加入配置
'controllers' => [
    'controller\\ApiTest',
],
```

2、为控制器加上一些注释，以让文档可读性更高（当然这不是必须的）
```php
<?php
namespace app\controller;
/**
 * @title Api接口文档测试
 * @desc 测试一些注释的解析能力
 */
class ApiTest
{
  ...    
}
```

此时刷新文档页面，得到一个控制器被解析

<img :src="$withBase('/images/apidoc-controller-demo.png')" style="width:100%;" alt="apidoc-controller-demo">


## 接口注释

控制器中的每一个符合注释规则的方法都会被解析成一个API接口

### 基础注释
先来体验一个最基本的注释，所得到的结果

我们在控制器中加入如下方法，如下

```php
<?php
namespace app\controller;
/**
 * @title Api接口文档测试
 * @desc 测试一些注释的解析能力
 */
class ApiTest
{ 
    /**
     * @title 基础的注释方法
     * @desc 一个很基础的接口注释解析能力
     * @author HG
     * @url /api/test
     * @method GET
     * @tag 测试 基础
     * @header name:Authorization require:1 desc:Token
     * @param name:username type:string require:1 desc:用户名
     * @param name:password type:string require:1 desc:登录密码MD5
     * @param name:phone type:string require:1 desc:手机号
     * @param name:sex type:int require:0 default:0 desc:性别
     * @return name:id type:int desc:新增用户的id
     */
    public function test(){
        return returnSuccess(["id"=>1]);
    }
  
}
```

以上注释，我们得到的效果如下
<img :src="$withBase('/images/apidoc-api-base-demo.png')" style="width:100%;" alt="apidoc-api-base-demo">


### 通用注释
通过定义通用的公共注释参数来实现 可复用性，避免每个接口都定义一大堆同样的参数

#### 1、增加配置
首先，在配置文件 config/apidoc.php 配置文件中，指定一个控制器为定义公共注释的控制器
```php
// config/apidoc.php
// 指定公共注释定义的文件地址
'definitions'=>"app\controller\Definitions",
```

#### 2、定义通用注释
添加一些通用的方法及注释，（定义param 与return 参数与定义接口书写规则一致）
```php
<?php
namespace app\controller;
class Definitions
{
    /**
     * @title 获取分页数据列表的参数
     * @param name:pageIndex type:int require:0 default:0 desc:查询页数
     * @param name:pageSize type:int require:0 default:20 desc:查询条数
     */
    public function pagingParam(){}
  
    /**
     * @title 返回字典数据
     * @return name:id type:int desc:唯一id
     * @return name:name type:string desc:字典名
     * @return name:value type:string desc:字典值
     */
    public function dictionary(){}
    
}
```

#### 3、使用定义
在接口注释中的 param 与 retrun 可通过 ref:definitions\XXX 来指定引入的 通用注释

```php
<?php
namespace app\controller;
/**
 * @title Api接口文档测试
 * @desc 测试一些注释的解析能力
 */
class ApiTest
{ 
    /**
     * @title 引入定义注释
     * @desc 引入定义文件所定义的通用参数来
     * @author HG
     * @url /api/test
     * @method GET
     * @param name:page type:object ref:definitions\pagingParam desc:分页参数
     * @param ref:definitions\pagingParam
     * @return name:list type:array ref:definitions\dictionary
     */
    public function test(){
        //...
    }
}
```
:::tip 以上param用了两种方式引入，分别是参数指定 字段名 name与 type ，与不指定字段名
- 指定字段名：会将引入的参数在该字段属性下，如下效果
- 不指定字段名：直接引入所有参数
:::

效果如下
<img :src="$withBase('/images/apidoc-api-dictionary-demo.png')" style="width:100%;" alt="apidoc-api-dictionary-demo">


### 逻辑层注释

在实际开发中，控制器只对参数做基础校验等处理，实际的业务逻辑处理通常会分层给逻辑层来处理（我这里把业务逻辑层叫server，您也可以根据自己开发来定义 业务逻辑层），我们可直接引入业务逻辑层的注释来实现接口参数的定义

#### 增加业务逻辑层
1、在项目 app 目录下新建 server 文件夹（您也可以叫别的）

2、在此文件夹下新建一个ApiDoc.php文件，内容如下：
```php
<?php
namespace app\servers;
class ApiDoc
{
    /**
     * @title 返回会员信息
     * @param name:id type:int require:1 desc:唯一id
     * @return name:id type:int desc:唯一id
     * @return name:name type:string desc:姓名
     * @return name:phone type:string desc:电话
     */
    public function getUserInfo(){}

    /**
     * @title 返回会员列表
     * @return ref:app\model\User\getList
     */
    public function getUserList(){}
}
```

#### 引用逻辑层注释

在控制器的接口注释中的 param 与 retrun 可通过 ref:app\servers\ApiDoc\getUserInfo来指定引入逻辑层的注释

```php
<?php
namespace app\controller;
/**
 * @title Api接口文档测试
 * @desc 测试一些注释的解析能力
 */
class ApiTest
{ 
    /**
     * @title 引入逻辑层定义注释
     * @desc 引入业务逻辑层的注释参数
     * @author HG
     * @url /api/server
     * @method GET
     * @param ref:app\servers\ApiDoc\getUserInfo
     * @return name:userInfo type:object ref:app\servers\ApiDoc\getUserInfo
     * @return name:userList type:array ref:app\servers\ApiDoc\getUserList
     */
    public function test(){
       ...
    }
}
```

:::tip 以上param请求参数直接引入了逻辑层定义的参数，return返回结果，引入逻辑层的两种方式返回
- 直接返回逻辑层定义的参数
- 逻辑层也可以再引入模型的数据表字段注释，从而减少注释量
:::

效果如下
<img :src="$withBase('/images/apidoc-api-server-demo.png')" style="width:100%;" alt="apidoc-api-server-demo">



### 模型注释
接口参数都与数据表息息相关，很多接口参数均由数据表字段而来。我们可以直接引入指定模型的数据表字段来生成参数说明，省去了一大堆接口注释与维护工作。

#### 给数据表字段添加注释

建议为数据表字段添加注释，即让数据表字段可读性更高，也让文档可读性更高。
我们直接在数据表给相应字段添加注释，如下SQL供参考

```php
CREATE TABLE `user` (↵  
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',↵  
`username` varchar(64) NOT NULL COMMENT '用户名',↵  
`nickname` varchar(64) DEFAULT NULL COMMENT '昵称',↵  
`password` char(64) NOT NULL COMMENT '登录密码',↵  
`regip` bigint(11) NOT NULL COMMENT '注册IP',↵  
`state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',↵  
`phone` char(32) DEFAULT NULL COMMENT '联系电话',↵  
`create_time` int(10) DEFAULT NULL COMMENT '创建时间',↵  
`login_num` int(11) unsigned DEFAULT NULL COMMENT '登录次数',↵  
`sex` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别',↵  
`delete_time` int(10) DEFAULT NULL,↵  
`role` varchar(64) DEFAULT NULL COMMENT '角色',↵  
`name` varchar(64) DEFAULT NULL COMMENT '姓名',↵  
PRIMARY KEY (`id`)↵) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8"
```

#### 模型方法的注释
可为引入的数据模型方法添加相应注释来实现 field（返回指定字段）、withoutField（排除指定字段）、addField（添加指定字段）

|参数|说明|书写规范|
|-|-|-|
|field|返回指定字段|英文格式逗号 , 分开指定的字段|
|withoutField|排除指定字段|英文格式逗号 , 分开指定的字段|
|addField|添加指定字段|可定义多个，每行为一个参数|
|   \|— name|参数的字段名|如：name:group_name|
|   \|— type|字段类型|int \| string \| ... 等|
|   \|— default|默认值|如：default:1|
|   \|— desc|字段说明文字||

```php
<?php
namespace app\model;

class User extends BaseModel
{
    /**
     * @title 根据id获取明细
     * @field id,username,nickname,state,sex
     * @addField name:group_name type:string desc:会员组名称
     * @addField name:role_name type:string desc:角色名称
     */
    public function getInfo($id){
        $res = $this->get($id);
        return $res;
    }
}
```
<img :src="$withBase('/images/apidoc-api-model-demo.png')" style="width:100%;" alt="apidoc-api-model-demo">
