# V1.0升级V2.0报错

由于V2.0是不向下兼容的重构版本，如从V1.0升级到V2.0可能会出现如下问题

- 未定义数组索引auth
- 方法不存在 hg\apidoc\Controller::getList()

## 解决方案1
升级前端文件

 [安装说明-添加前端页面](/install/#添加前端页面) 下载最新的文件覆盖原来的public/apidoc目录

## 解决方案2

确认config/apidoc.php配置文件为V2.x版本的配置文件

参考 [config/apidoc.php](https://github.com/HGthecode/thinkphp-apidoc/blob/master/src/config.php)
