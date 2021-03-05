---
sidebar: auto
---

# 使用phpstudy搭建TP6运行环境

## 下载安装包
访问 phpStudy 官网 [https://www.xp.cn](https://www.xp.cn)，选择自己的系统下载

![下载php study 安装包](/thinkphp-apidoc/images/course/php-study-install/install-1.png "install-1")




## 安装

将下载下来的安装包，解压出来，双击.exe文件

![安装php study1](/thinkphp-apidoc/images/course/php-study-install/install-2.png "install-2")

会看到这个界面

![安装php study2](/thinkphp-apidoc/images/course/php-study-install/install-3.png "install-3")

默认会安装到C盘，可点击`自定义选项`，改变存储位置，并点击立即安装即可

![安装php study3](/thinkphp-apidoc/images/course/php-study-install/install-4.png "install-4")


## 环境配置

1、启动服务：进入phpStydy系统会看到这个界面，启动`WNPM`，并启动`Apache`或`Nginx`，与`MySQL`服务

![启动php study服务](/thinkphp-apidoc/images/course/php-study-install/install-5.png "install-5")

2、安装php7以上版本:由于TP6要求php版本>=7.1，进入软件管理，点击php分类，选择一个大于7.1.0的版本安装

![安装php7版本](/thinkphp-apidoc/images/course/php-study-install/install-7.png "install-7")


## 创建站点

1、域名：任意填写一个，建议不要填常用的域名，否则会导致正常网站无法访问。比如：此处域名设置为`www.baidu.com`，当访问`www.baidu.com`时就会host访问到本地的站点，而不是百度，就会影响你对百度的正常使用了。

![创建站点](/thinkphp-apidoc/images/course/php-study-install/install-6.png "install-6")

2、根目录：点击`浏览`，选择你的TP项目目录的 `public`目录，如果还没有TP项目，可参考[从0创建TP6新项目与Apidoc的安装](./createTpAndInstall.md)此篇文章创建。

3、php版本：TP6必须选择>7.1版本的php

创建站点成功后,就可用浏览器访问你设置的域名如：`www.test.com`，显示如下页面就表示部署成功了

![访问站点](/thinkphp-apidoc/images/course/php-study-install/install-8.png "install-8")