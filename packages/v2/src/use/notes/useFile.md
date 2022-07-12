# 引入解释文件

控制器必须引入注释解释文件，否则会出现`[Semantical Error] The annotation...`的报错

## 引入方式一：
```php
<?php
namespace app\controller;
// 添加这句，注释写法为 @Apidoc\参数名(...)
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("基础示例")
 * @Apidoc\Group("base")
 */
class ApiDocTest
{
   /**
    * @Apidoc\Title("测试接口")
    * ...
    */ 
    public function index(){
        //...
    }
}
```

## 引入方式二：
```php
<?php
namespace app\controller;
// 分别引入这些解释文件，注释写法为 @参数名(...)
use hg\apidoc\annotation\Title;
use hg\apidoc\annotation\Group;
use hg\apidoc\annotation\Desc;
use hg\apidoc\annotation\Author;
use hg\apidoc\annotation\Url;
use hg\apidoc\annotation\Tag;
use hg\apidoc\annotation\Param;
use hg\apidoc\annotation\Returned;
// ...

/**
 * @Title("基础示例")
 * @Group("base")
 */
class ApiDocTest
{
   /**
    * @Title("测试接口")
    * ...
    */ 
    public function index(){
        //...
    }
}
```