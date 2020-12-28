## 安装
> 在安装本插件时，默认你已成功安装ThinkPHP6的项目并成功运行


### 安装插件
1、进入ThinkPHP6 项目根目录，执行如下命令：
```sh
composer require hg/apidoc
```

2、添加路由
将 vendor/hg/apidoc/route/apidoc.php 复制到项目目录route目录下

### 添加前端页面
 1、执行以下命令下载UI
```sh
git clone https://github.com/HGthecode/apidoc-ui.git
```
 2、下载完成后，找到dist目录，将该目录放到你的项目public目录下，并将dist文件夹名，重命名为      apidoc


安装插件完成后 打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功

接下来，了解一下  [基本配置](/config/) 和 如何编写 注释 吧。

