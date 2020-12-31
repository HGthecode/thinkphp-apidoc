## 安装
::: tip 在安装本插件时，确保你已成功安装ThinkPHP6的项目并成功运行
安装方法参考：[ThinkPHP6文档](https://www.kancloud.cn/manual/thinkphp6_0/1037481)
:::


### 安装插件
1、进入ThinkPHP6 项目根目录，执行如下命令：
```sh
composer require hg/apidoc
```

2、添加路由
将以下路由加入到 `route/app.php`

```php
Route::get('apidoc/config', "\\hg\\apidoc\\Controller@getConfig");
Route::get('apidoc/data', "\\hg\\apidoc\\Controller@getList");
Route::post('apidoc/auth', "\\hg\\apidoc\\Controller@verifyAuth");
```


### 添加前端页面

#### 方式一 

[点击下载](https://apidoc.demo.hg-code.com/download/apidoc.zip) ，下载完成后解压，将apidoc文件夹拷贝到你的项目 public 目录下

#### 方式二 GitHub 下载
 1、执行以下命令下载UI
```sh
git clone https://github.com/HGthecode/apidoc-ui.git
```
 2、下载完成后，将dist目录放到你的项目public目录下，并将dist文件夹名，重命名为`apidoc`

安装插件完成后 打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功

接下来，了解一下  [基本配置](/config/) 和 [如何使用](/use/) 吧。

