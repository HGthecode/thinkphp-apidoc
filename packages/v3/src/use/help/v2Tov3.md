# V2.x升级V3.x指南

由于V3.0是不向下兼容的重构版本，如从V2.x升级到V3.0可参考本文进行升级

## 升级扩展至3.0

项目根目录执行

```sh
composer update hg/apidoc
```

## 更新前端文件

[Apidoc UI v2.0.1](https://gitee.com/hg-code/apidoc-ui/attach_files/817036/download/apidoc.zip)


## 调整配置

由于之前安装过2.x生成过该配置文件了，升级后该配置文件不会重新生成

可将 `/vendor/hg/apidoc/src/config.php` 的内容拷贝到原`config/apidoc.php`中，并参考[配置参数](/config/)进行配置





