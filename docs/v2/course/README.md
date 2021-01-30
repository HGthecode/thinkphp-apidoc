# 从0开始快速上手

## ThinkPHP6 安装
> 本步骤也可根据 ThinkPHP [官方文档](https://www.kancloud.cn/manual/thinkphp6_0/1037481)
### 1、首先根据官方文档，执行如下命令安装

```sh
composer create-project topthink/think tp6-test
```
<img class="img-view" :src="$withBase('/images/v2/course/install-1.png')" style="width:100%;" alt="install-1">


### 2、等待一会儿出现如下显示，表示安装成功了

<img class="img-view" :src="$withBase('/images/v2/course/install-2.png')" style="width:100%;" alt="install-2">

### 3、进入项目目录，测试运行

```sh
# 进入项目目录
cd tp6-test

# 启动项目
php think run
```
<img class="img-view" :src="$withBase('/images/v2/course/install-3.png')" style="width:100%;" alt="install-3">

浏览器访问 http://localhost:8000/，出现如下显示，表示TP6安装成功

<img class="img-view" :src="$withBase('/images/v2/course/install-4.png')" style="width:100%;" alt="install-4">



## 安装Apidoc 插件

### 1、根目录执行如下命令安装插件
```sh
composer require hg/apidoc
```
<img class="img-view" :src="$withBase('/images/v2/course/install-apidoc-1.png')" style="width:100%;" alt="install-apidoc-1">

### 2、下载前端文件

进入[官网文档安装页面](/v2/install)，如下图点击下载前端文件
<img class="img-view" :src="$withBase('/images/v2/course/install-apidoc-2.png')" style="width:100%;" alt="install-apidoc-2">

下载完成后，将文件解压，将apidoc文件夹拷贝到TP6的 `public`目录下
<img class="img-view" :src="$withBase('/images/v2/course/install-apidoc-3.png')" style="width:100%;" alt="install-apidoc-3">

浏览器访问 `http://localhost:8000/apidoc/`，出现以下页面表示安装成功

<img class="img-view" :src="$withBase('/images/v2/course/install-apidoc-4.png')" style="width:100%;" alt="install-apidoc-4">

::: warning 建议
使用`php think run`启动项目，可能会出现如下问题，推荐使用环境集成工具配置站点访问。
:::
<img class="img-view" :src="$withBase('/images/v2/course/install-apidoc-6.png')" style="width:100%;" alt="install-apidoc-6">


## 注解体验

打开`app/cotroller/Index.php`，修改为如下代码
```php
<?php
namespace app\controller;

use app\BaseController;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\title("基础示例")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\title("基础的注释方法")
     * @Apidoc\Desc("最基础的接口注释写法")
     * @Apidoc\url("/index/index")
     * @Apidoc\method("GET")
     * @Apidoc\tag("测试 基础")
     * @Apidoc\header("Authorization", require=true, desc="Token")
     * @Apidoc\param("username", type="string",require=true, desc="用户名" )
     * @Apidoc\param("password", type="string",require=true, desc="密码" )
     * @Apidoc\param("phone", type="string",require=true, desc="手机号" )
     * @Apidoc\param("sex", type="int",default="1",desc="性别" )
     * @Apidoc\Returned("id", type="int", desc="新增用户的id")
     */
    public function index()
    {
        return 1;
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
```
刷新文档页面，得到如下效果
<img class="img-view" :src="$withBase('/images/v2/course/install-apidoc-5.png')" style="width:100%;" alt="install-apidoc-5">






