---
sidebar: auto
---

# 接口参数Param、Returned注解技巧


## 参数重写

当同级参数存在相同的参数时后者覆盖前者

应用场景：如ref引入了一些参数，但当前接口需要对其中某些参数进行重写

### 基础重写示例
```php
/**
 * @Apidoc\Param("name", type="string",desc="姓名" )
 * @Apidoc\Param("name", type="string",require=true,desc="重写姓名字段" )
 */
```

文档效果：

|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|name|string| √ | |重写姓名字段|

### ref引入重写基础

假设User表字段如下：
```php
CREATE TABLE `user` (↵  
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `name` varchar(32) NOT NULL COMMENT '姓名',
  `age` int(3) DEFAULT NULL COMMENT '年龄',
  `sex` tinyint(1) unsigned DEFAULT '1' COMMENT '性别',
PRIMARY KEY (`id`)↵) ENGINE=MyISAM DEFAULT CHARSET=utf8"
```

```php
/**
 * @Apidoc\Param(ref="app\model\User\getInfoById")
 * @Apidoc\Param("name",type="string",require=true,desc="姓名重写")
 */
```

文档效果：

|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|id|int|  | |用户id|
|name|string| √ | |姓名重写|
|age|int|  | |年龄|
|sex|tinyint|  | 1 |性别|



### ref引入重写子参数
> 假设User表字段同上

在apidoc解析中，当ref定义了字段名及类型时，会将ref的参数作为该字段（object、array类型时）的子参数，我们可以通过以下方式重写或增加字段：

```php
/**
 * @Apidoc\Param("userInfo",type="object",ref="app\model\User\getInfoById",desc="用户信息",
 *    @Apidoc\Param("name",type="string",require=true,desc="姓名重写"),
 *    @Apidoc\Param("phone",type="string",desc="联系电话")
 * )
 */
```

文档效果：

|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- userInfo|object|  | |用户信息|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;id|int|  | |用户id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name|string| √ | |姓名重写|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;age|int|  | |年龄|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sex|tinyint|  | 1 |性别|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;phone|string|  |  |联系电话|


### 统一响应体重写

> 假设User表字段同上

当某些接口所需返回的数据结构与配置的统一响应体字段不一致时，可通过定义配置字段的replaceGlobal="true"来覆盖统一响应体的对应字段

假设你的统一响应体配置如下：
```php
// config/apidoc.php
// 统一的请求响应体
'responses'=>[
    ['name'=>'code','desc'=>'状态码','type'=>'int'],
    ['name'=>'message','desc'=>'操作描述','type'=>'string'],
    ['name'=>'data','desc'=>'业务数据','main'=>true,'type'=>'object'],
],
```

接口注解：

```php
/**
* @Apidoc\Returned("data", type="array",childrenType="object", desc="返回数据",replaceGlobal="true",
*      @Apidoc\Returned("total",type="int",desc="总条数"),
*      @Apidoc\Returned("list", type="array",ref="app\model\User\getList",withoutField="delete_time",desc="列表数据"),
* )
 */
```

文档效果：

|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- code|int|  | |状态码|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;message|string|  | |操作描述|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;data|array<\object>|  | |返回数据|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;total|int|  | |总条数|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;list|array|  |  |列表数据|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;id|int|  | |用户id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name|string| √ | |姓名|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;age|int|  | |年龄|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sex|tinyint|  | 1 |性别|



