---
sidebar: false
---

::: warning 注意
v2.0版本已发布，建议使用功能更丰富的最新版本：[v2.0文档](/v2/)
:::


### 安装插件
进入ThinkPHP 项目根目录，执行如下命令：
```sh
composer require hg/apidoc:1.1.x
```



### 添加前端页面

#### 方式一 (推荐)

[点击下载](https://github.com/HGthecode/apidoc-ui/releases/download/v1.0.0/apidoc.zip) ，下载完成后解压，将apidoc文件夹拷贝到你的项目 public 目录下

#### 方式二 GitHub 下载
 1、执行以下命令下载UI
```sh
git clone https://github.com/HGthecode/apidoc-ui.git
```
 2、下载完成后，将`apidoc`目录放到你的项目public目录下

安装插件完成后 打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功。

>TP5.x版本用户出现访问页面接口404的情况，请查看 [TP5.x版本文档接口404](/use/help/#TP5.x版本文档接口404)

接下来，了解一下  [基本配置](/config/) 和 [如何使用](/use/) 吧。



## 升级插件

由于>=v2.0版本不兼容v1.0请升级到v2.0版本后，根据[v2.0文档](/v2/)配置及编写注释