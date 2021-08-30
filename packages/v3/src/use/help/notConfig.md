# 没有生成apidoc.php配置文件

TP5版本，或使用`composer2`，且`topthink/framework`版本小于 V6.0.6，,安装时可能会出现无法自动生成配置文件

## 解决方案1：
手动将 `/vendor/hg/apidoc/src/config.php` 拷贝到`/config/`目录下，并重命名为`apidoc.php`


## 解决方案2（TP6）：
执行 `composer update topthink/framework` 将thinkphp主框架升级到最新，再安装本插件

