

# 调试时的事件

接口调试时分别触发`before`请求发起前事件，与`after`请求响应后事件。

可通过接口的注解来定义执行的事件，如下：

```php
/**
 * @Apidoc\Title ("调试时事件")
 * @Apidoc\Url("/admin/demo/debug")
 * @Apidoc\Param("name",type="string",desc="姓名")
 * @Apidoc\Param("phone",type="string",desc="性别")
 * @Apidoc\Param("myValue",type="string",desc="我的临时请求头参数")
 * @Apidoc\Before(event="setHeader",key="myHeader",value="params.myValue")
 * @Apidoc\Before(event="clearParam",key="myValue")
 * @Apidoc\After(event="setGlobalHeader",key="myGHeader",value="res.data.data.myGHeader",desc="我的全局Header参数")
 * @Apidoc\After(event="setGlobalParam",key="myGParam",value="123456",desc="我的全局参数")
 */
public function debug(Request $request){
    //...
}
```

以上例子中，会执行以下事件：

1、请求发起前，设置一个请求头参数`myHeader`，参数值为请求参数中的`myValue`参数。

2、请求发起前，清除请求参数`myValue`。

3、请求响应后，设置一个全局请求头参数`myGHeader`，参数值为响应结果中返回数据中的`data.myGHeader`。

4、请求响应后，设置一个全局请求参数`myGParam`，参数值为`123456`。


## Before|After注解的参数

|参数名|说明|
|-|-|-|
| event |	事件名 |  
| key |	字段名 |  
| value |	字段值，可直接设置值，或以`params.xxx`取请求参数中的值；以`res.xxx`取请求响应结果中的参数 |  
| desc |	字段描述 |  
| url |	event为ajax时，定义请求地址 |  
| method |	event为ajax时，定义请求类型 |  
| contentType |	event为ajax时，定义contentType |  



## 事件说明


### setHeader

> `before`请求发起前有效

设置一个请求头参数

`@Apidoc\Before(event="setHeader",key="参数名",value="参数值")`


### setGlobalHeader

设置一个全局请求头参数

`@Apidoc\After(event="setGlobalHeader",key="参数名",value="参数值",desc="参数描述")`


### clearGlobalHeader

清除一个全局请求头参数

`@Apidoc\Before(event="clearGlobalHeader",key="参数名")`






### setParam

> `before`请求发起前有效

设置一个请求参数

`@Apidoc\Before(event="setParam",key="参数名",value="参数值")`

### clearParam

清除一个请求参数

`@Apidoc\Before(event="clearParam",key="参数名")`


### handleParam

处理一个请求参数，key仅支持`md5`

`@Apidoc\Before(event="handleParam",key="md5",value="参数值")`


### setGlobalParam

设置一个全局请求参数

`@Apidoc\Before(event="setGlobalParam",key="参数名",value="参数值")`



### clearGlobalParam

清除一个全局请求参数

`@Apidoc\Before(event="clearGlobalParam",key="参数名")`


### ajax

发送一个请求

```php
/**
 * @Apidoc\Before(event="ajax",url="请求地址",method="请求类型",contentType="appicateion-json",
 *    @Apidoc\Before(event="setParam",key="key",value="params.phone"),
 *    @Apidoc\Before(event="setParam",key="abc",value="123456"),
 *    @Apidoc\After(event="setHeader",key="X-CSRF-TOKEN",value="res.data.data")
 * )
 * /
```

以上注解，会在接口调试前发送一个请求，请求参数为`{key:"这个值为调试接口参数的phone字段",abc:"123456"}`，请求响应后执行`setHeader`设置一个key为`X-CSRF-TOKEN`的请求头参数，值为该请求返回值中的`res.data.data`




