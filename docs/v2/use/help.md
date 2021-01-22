# 常见问题


## 没有config/apidoc.php配置文件
如使用`composer2`以上版本，且`topthink/framework`版本小于 V6.0.6，安装时会出现无法自动生成配置文件

#### 解决方案1：
执行 `composer update topthink/framework` 将thinkphp主框架升级到最新，再安装本插件

#### 解决方案2：
手动将 `/vendor/hg/apidoc/src/config.php` 拷贝到`/config/`目录下，并重命名为`apidoc.php`


## 500注解报错
所有注释参数，需`use`引入注释解释文件，如出现以下错误
<img class="img-view" :src="$withBase('/images/v2/apidoc-help-use_error.png')" style="width:100%;" alt="apidoc-help-use_error">


可在相应的控制器use解释文件

```php
<?php
namespace app\controller;

// 加上这句
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\title("基础示例")
 */
class BaseDemo
{
    //...
}
```
更多处理方式可查阅 [doctrine/annotations](https://github.com/doctrine/annotations) 


