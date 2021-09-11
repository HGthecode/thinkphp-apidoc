---
sidebar: auto
---

# TP6新项目的创建与Apidoc的安装


## ThinkPHP6 安装
> 本步骤也可根据 ThinkPHP [官方文档](https://www.kancloud.cn/manual/thinkphp6_0/1037481)
### 1、首先根据官方文档，执行如下命令安装

```sh
composer create-project topthink/think tp6-test
```
![install-1](/thinkphp-apidoc/images/course/install-1.png "install-1")

### 2、等待一会儿出现如下显示，表示安装成功了

![install-2](/thinkphp-apidoc/images/course/install-2.png "install-2")

### 3、进入项目目录，测试运行

```sh
# 进入项目目录
cd tp6-test

# 启动项目
php think run
```
![install-3](/thinkphp-apidoc/images/course/install-3.png "install-3")

浏览器访问 http://localhost:8000/，出现如下显示，表示TP6安装成功

![install-4](/thinkphp-apidoc/images/course/install-4.png "install-4")


## 安装Apidoc 插件

### 1、根目录执行如下命令安装插件
```sh
composer require hg/apidoc
```

![install-apidoc-1](/thinkphp-apidoc/images/course/install-apidoc-1.png "install-apidoc-1")

### 2、下载前端文件

进入[官网文档安装页面](/guide/install/#添加前端页面)，如下图点击下载前端文件

![install-apidoc-2](/thinkphp-apidoc/images/course/install-apidoc-2.png "install-apidoc-2")

下载完成后，将文件解压，将apidoc文件夹拷贝到TP6的 `public`目录下

![install-apidoc-3](/thinkphp-apidoc/images/course/install-apidoc-3.png "install-apidoc-3")

浏览器访问 `http://localhost:8000/apidoc/`，出现以下页面表示安装成功

![install-apidoc-4](/thinkphp-apidoc/images/course/install-apidoc-4.png "install-apidoc-4")

::: warning 建议
使用`php think run`启动项目，可能会出现如下问题，推荐使用环境集成工具配置站点访问，可参考[使用phpstudy搭建TP6运行环境](./phpStudyInstall.md)。
:::

![install-apidoc-6](/thinkphp-apidoc/images/course/install-apidoc-6.png "install-apidoc-6")

## 注解体验

打开`app/cotroller/Index.php`，修改为如下代码

```php
<?php
namespace app\controller;

use app\BaseController;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("基础示例")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("基础的注释方法")
     * @Apidoc\Desc("最基础的接口注释写法")
     * @Apidoc\Url("/index/index")
     * @Apidoc\Method("GET")
     * @Apidoc\Tag("测试 基础")
     * @Apidoc\Header("Authorization", require=true, desc="Token")
     * @Apidoc\Param("username", type="string",require=true, desc="用户名" )
     * @Apidoc\Param("password", type="string",require=true, desc="密码" )
     * @Apidoc\Param("phone", type="string",require=true, desc="手机号" )
     * @Apidoc\Param("sex", type="int",default="1",desc="性别" )
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

![install-apidoc-5](/thinkphp-apidoc/images/course/install-apidoc-5.png "install-apidoc-5")






