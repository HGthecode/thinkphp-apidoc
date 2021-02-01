# 常见问题


## 没有config/apidoc.php配置文件
如使用`composer2`以上版本，且`topthink/framework`版本小于 V6.0.6，安装时会出现无法自动生成配置文件

#### 解决方案1：
执行 `composer update topthink/framework` 将thinkphp主框架升级到最新，再安装本插件

#### 解决方案2：
手动将 `/vendor/hg/apidoc/src/config.php` 拷贝到`/config/`目录下，并重命名为`apidoc.php`



## 500注解报错
所有文件注释中存在 @XXX 的，都需`use`引入注释解释文件，如出现以下错误
<img class="img-view" :src="$withBase('/images/v2/apidoc-help-use_error.png')" style="width:100%;" alt="apidoc-help-use_error">

可根据提示在相应的文件里，加上use解释文件

```php
<?php
namespace app\controller;

// 加上这句
use hg\apidoc\annotation as Apidoc;
// 通过use自定义解释文件，解决下面@abc的错误
// use app\utils\Abc;

/**
 * @Apidoc\Title("基础示例")
 */
class BaseDemo
{
    /**
     * @Apidoc\Title("引入通用注释")
     * @abc 错误示例，这样不存在解释文件的注释会报错；可增加use解释文件，或去除@
     */
    public function test(){
        //...
    }
}
```
自定义解释文件
```php
// app/utils/Abc.php 解释文件内容
<?php
namespace app\utils;
use Doctrine\Common\Annotations\Annotation;

/**
 * 自定义参数解释文件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Abc extends Annotation
{}
```

更多处理方式可查阅 [doctrine/annotations](https://github.com/doctrine/annotations) 


## V1.0升级到V2.0报错
由于V2.0是不向下兼容的重构版本，如从V1.0升级到V2.0可能会出现如下问题
- 未定义数组索引auth
- 方法不存在 hg\apidoc\Controller::getList()

#### 解决方案
##### 1、升级前端文件
 [安装说明-添加前端页面](/v2/install/#添加前端页面) 下载最新的文件覆盖原来的public/apidoc目录

##### 2、确认config/apidoc.php配置文件为V2.x版本的配置文件
参考 [config/apidoc.php](https://github.com/HGthecode/thinkphp-apidoc/blob/master/src/config.php)
