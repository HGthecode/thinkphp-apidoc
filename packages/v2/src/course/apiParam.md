---
sidebar: auto
---

# 各种参数类型的注解教程

:::tip 
本文注解用@Apidoc\Param（请求参数）举例，如果是返回参数，使用@Apidoc\Returned即可
:::
使用apidoc对接口的请求参数Param与响应参数Returned，根据所需的参数类型，可参考以下示例实现你的接口参数的注解


## String 字符串

#### 注解：
```php
/**
 * @Apidoc\Param("name", type="string",desc="姓名" )
 */
```

#### json效果：
```json
name:"";
```


#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|name|string| | |姓名|


## Int 整型

#### 注解：
```php
/**
 * @Apidoc\Param("age", type="int",desc="年龄" )
 */
```
#### json效果：
```json
age:0;
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|age|int| | |年龄|


## Boolean 布尔

#### 注解：
```php
/**
 * @Apidoc\Param("isCheck", type="boolean",default=false,desc="是否确认" )
 */
```
#### json效果：
```json
isCheck:false;
```
#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|isCheck|boolean| | false |是否确认|


## date 日期
> 实际为字符串类型，调试时将自动生成当天日期为该字段参数

#### 注解：
```php
/**
 * @Apidoc\Param("birthday", type="date",desc="生日" )
 */
```
#### json效果：
```json
birthday:"YYYY-MM-DD";
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|birthday|date| |  |生日|


## time 时间
> 实际为字符串类型，调试时将自动生成当前时间为该字段参数

#### 注解：
```php
/**
 * @Apidoc\Param("class_time", type="time",desc="上课时间" )
 */
```
#### json效果：
```json
class_time:"HH:mm:ss";
```
#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|class_time|time| |  |上课时间|


## datetime 日期时间
> 实际为字符串类型，调试时将自动生成当前日期时间为该字段参数

#### 注解：
```php
/**
 * @Apidoc\Param("create_time", type="datetime",desc="创建时间" )
 */
```
#### json效果：
```json
create_time:"YYYY-MM-DD HH:mm:ss";
```
#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|create_time|datetime| |  |创建时间|



## file 文件类型
> 当ParamType为formdata时，file类型的字段在调试时为附件上传选择器

#### 注解：
```php
/**
 * @Apidoc\ParamType("formdata")
 * @Apidoc\Param("file",type="file", require=true,desc="附件")
 */
```
#### json效果：
```json
{
  file: "file",  // 附件
}
```
#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|file|file| |  |附件|



## object 对象

#### 注解：
```php
/**
 * @Apidoc\Returned("userInfo", type="object", desc="用户信息",
 *    @Apidoc\Returned("id", type="int", desc="唯一id"),
 *    @Apidoc\Returned("name", type="string", desc="姓名"),
 *    @Apidoc\Returned("sex", type="int", desc="性别"),
 * )
 */
```

#### json效果：
```json
userInfo:{
    id:0,
    name:"",
    sex:0,
}
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- userInfo|object| |  |用户信息|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;id|int|  |  |唯一id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name|string| |  |姓名|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sex|int| |  |性别|



## array 数组

> 默认与array<\object>对象数组相等

#### 注解：
```php
/**
 * @Apidoc\Param("userList", type="array", desc="用户列表",
 *    @Apidoc\Param("id", type="int", desc="唯一id"),
 *    @Apidoc\Param("name", type="string", desc="姓名"),
 *    @Apidoc\Param("sex", type="int", desc="性别"),
 * )
 */
```

#### json效果：
```json
userList: [    //用户列表
    {
      id: 0,  // 唯一id
      name: "string",  // 姓名
      sex: 0,  // 性别
    }
]
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- userList|array| |  |用户信息|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;id|int|  |  |唯一id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name|string| |  |姓名|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sex|int| |  |性别|


## array\<string> 字符串数组

#### 注解：
```php
/**
 * @Apidoc\Returned("codeList", type="array",childrenType="string", desc="用户列表")
 */
```

#### json效果：
```json
codeList: [],  // 编码列表
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|codeList|array\<string\>| |  |编码列表|



## array\<int> 整型数组

#### 注解：
```php
/**
 * @Apidoc\Param("idList", type="array",childrenType="int", desc="id列表")
 */
```

#### json效果：
```json
idList: [],  // id列表
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|codeList|array\<int\>| |  |id列表|



## array\<object> 对象数组

#### 注解：
```php
/**
 * @Apidoc\Param("userList", type="array",childrenType="object", desc="用户列表",
 *    @Apidoc\Param("id", type="int", desc="唯一id"),
 *    @Apidoc\Param("name", type="string", desc="姓名"),
 *    @Apidoc\Param("sex", type="int", desc="性别"),
 *)
 */
```

#### json效果：
```json
userList: [    //用户列表
    {
      id: 0,  // 唯一id
      name: "string",  // 姓名
      sex: 0,  // 性别
    }
],
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- userList|array| |  |用户信息|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;id|int|  |  |唯一id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name|string| |  |姓名|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sex|int| |  |性别|
 

## array\<array> 二维字符串数组

#### 注解：
```php
/**
 * @Apidoc\Param("codeList", type="array",childrenType="array", desc="编码组列表",
 *    @Apidoc\Param("code", type="string", desc="编码"),
 * )
 */
```

#### json效果：
```json
codeList: [    //编码组列表
    [
      "string",  // 编码
    ]
  ]
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- codeList|array<\array>| |  |编码组列表|
||string|  |  |编码|



## array\<array> 二维对象数组

#### 注解：
```php
/**
 * @Apidoc\Param("userListGroup", type="array",childrenType="array", desc="用户列表组",
 *    @Apidoc\Param("userList", type="object", desc="用户列表",
 *       @Apidoc\Param("id", type="int", desc="用户id"),
 *       @Apidoc\Param("name", type="string", desc="姓名"),
 *    )
 * )
 */
```

#### json效果：
```json
userListGroup: [    //用户列表组
    [
      {    //用户列表
        id: 0,  // 用户id
        name: "string",  // 姓名
      },
    ]
  ],
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- userListGroup|array<\array>| |  |用户列表组|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -|object|  |  |用户列表|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - id|int|  |  |用户id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - name|string|  |  |姓名|





## tree 树形

#### 注解：
```php
/**
 * @Apidoc\Param("menuTree",type="tree", desc="菜单树",childrenField="children",childrenDesc="二级菜单",
 *     @Apidoc\Param("id",type="int", desc="菜单id"),
 *     @Apidoc\Param("name",type="string", desc="菜单名称"),
 *     @Apidoc\Param("icon",type="string", desc="菜单图标")
 * )
 */
```

#### json效果：
```json
menuTree: [    //菜单树
  {
    id: 0,
    name: "string",
    icon: "string",
    children: [    
      {
        id: 0,
        name: "string",
        icon: "string",
      }
    ],
  }
],
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- menuTree|tree| |  |菜单树|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   id|int|  |  |菜单id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   name|string|  |  |菜单名称|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   icon|string|  |  |菜单图标|
|&nbsp;&nbsp;&nbsp; - children|string|  |  |二级菜单|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   id|int|  |  |菜单id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   name|string|  |  |菜单名称|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   icon|string|  |  |菜单图标|




## tree 自定义树形

#### 注解：
```php
/**
 * @Apidoc\Param("authTree",type="tree", desc="权限树",
 *     @Apidoc\Param("id",type="int", desc="模块id"),
 *     @Apidoc\Param("name",type="string", desc="模块名称"),
 *     @Apidoc\Param("code",type="string", desc="模块编码"),
 *     @Apidoc\Param("groups",type="array", desc="业务分组",
 *        @Apidoc\Param("id",type="int", desc="业务id"),
 *        @Apidoc\Param("name",type="string", desc="业务名称"),
 *        @Apidoc\Param("code",type="string", desc="业务编码"),
 *        @Apidoc\Param("actions",type="array", desc="功能",
 *           @Apidoc\Param("id",type="int", desc="功能id"),
 *           @Apidoc\Param("name",type="string", desc="功能名称"),
 *           @Apidoc\Param("code",type="string", desc="功能编码")
 *        )
 *     ),
 * )
 */
```

#### json效果：
```json
authTree: [    //权限树
  {
    id: 0,
    name: "string",
    code: "string",
    groups: [    
      {
        id: 0,
        name: "string",
        code: "string",
        actions: [    
          {
            id: 0,
            name: "string",
            code: "string",
          }
        ],
      }
    ],
  }
],
```

#### 文档效果：
|名称|类型|必填|默认值|说明|
|-|-|-|-|-|
|- authTree|tree| |  |权限树|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   id|int|  |  |模块id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   name|string|  |  |模块名称|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   code|string|  |  |模块编码|
|&nbsp;&nbsp;&nbsp; - groups|array|  |  |业务分组|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   id|int|  |  |业务id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   name|string|  |  |业务名称|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   code|string|  |  |业务编码|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - actions|array|  |  |功能|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   id|int|  |  |功能id|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   name|string|  |  |功能名称|
|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   code|string|  |  |功能编码|



