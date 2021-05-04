---
icon: install
category: 指南
sidebarDepth: 2
---

# 安装/更新

::: tip 在安装本插件时，确保你已成功安装ThinkPHP6的项目并成功运行
安装方法参考：[ThinkPHP6文档](https://www.kancloud.cn/manual/thinkphp6_0/1037481)
:::


## 安装插件
进入ThinkPHP6 项目根目录，执行如下命令：
```sh
composer require hg/apidoc
```



## 添加前端页面


|前端版本(点击下载)|与apidoc版本对照|
|-|-|
|[v1.3.3](https://github.com/HGthecode/apidoc-ui/releases/download/v1.3.3/apidoc.zip)| 适用于`v2.5.0` - `v2.5.2`版本 |
|[v1.2.1](https://github.com/HGthecode/apidoc-ui/releases/download/v1.2.1/apidoc.zip)| 适用于`v2.4.2`版本 |
|[v1.2.0](https://github.com/HGthecode/apidoc-ui/releases/download/v1.2.0/apidoc.zip)| 适用于`v2.4.0` - `v2.4.1`版本 |
|[v1.1.0](https://github.com/HGthecode/apidoc-ui/releases/download/v1.1.0/apidoc.zip)| 适用于`v2.0.0` - `v2.3.0`版本 |
|[v1.0.0](https://github.com/HGthecode/apidoc-ui/releases/download/v1.0.0/apidoc.zip)| 适用于`v1.x` 版本 |

下载完成后解压，将apidoc文件夹拷贝到你的项目 public 目录下

安装插件完成后 打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功。

接下来，了解一下  [基本配置](/config/) 和 [如何使用](/use/) 吧。

>如遇页面报错或无法正常使用，可参考[常见问题](/use/help/)



## 升级插件

1、如果你之前已经安装过，那么切换到你的应用根目录下面，然后执行下面的命令进行更新。
```sh
composer update hg/apidoc
```
2、更新前端页面，可通过安装时的[添加前端页面](/install/#添加前端页面) 方式获得最新的前端文件，覆盖/public/apidoc的文件夹即可。
