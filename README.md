<p align="center">
  <a href="#">
    <img width="120" src="https://apidoc.demo.hg-code.com/images/logo.png">
  </a>
</p>

<h1 align="center">
  <a target="_blank">ThinkPHP ApiDoc</a>
</h1>

<div align="center">

基于ThinkPHP 根据注释自动生成API接口文档

</div>


### 文档

[ThinkPHP ApiDoc V2.x文档](https://hg-code.gitee.io/thinkphp-apidoc/)

[ThinkPHP ApiDoc V1.x文档](https://hg-code.gitee.io/thinkphp-apidoc/v1/)

### 特性

- 开箱即用：安装后按文档编写注释即可。
- 在线调试：支持设置全局请求头Authorize，接口调试省时省力。
- 轻松编写：支持公共注释定义、业务逻辑层、数据表字段注释的引用。
- 版本管理：可生成不同版本的API接口文档，任意切换。
- 控制器分组：可通过控制器分组实现多模块/应用的接口分组。
- Markdown文档：支持读取.md文件展示文档。
- 安全验证：支持密码验证访问接口文档。
- 高效缓存：支持缓存接口数据，无需每次都生成数据。
- 完善的文档及使用示例。

### 兼容

- ThinkPHP 6.x 

> 如需在 `ThinkPHP 5.x` 版本中使用，请安装 `V1.x`版本 [V1.x版本安装说明](https://hgthecode.github.io/thinkphp-apidoc/v1/install/)

### 安装
进入ThinkPHP 项目根目录，执行如下命令：
```sh
composer require hg/apidoc
```

#### 添加前端页面

##### 方式一 (推荐)

[点击下载](https://apidoc.demo.hg-code.com/download/apidoc.zip) ，下载完成后解压，将apidoc文件夹拷贝到你的项目 public 目录下

##### 方式二 GitHub 下载
 1、执行以下命令下载UI
```sh
git clone https://github.com/HGthecode/apidoc-ui.git
```
 2、下载完成后，将`apidoc`目录放到你的项目public目录下

安装插件完成后 打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功


### 支持我们
如果本项目对您有所帮助，请点个Star支持我们

### 鸣谢
[ThinkPHP](http://www.thinkphp.cn/) 

[doctrine/annotations](https://github.com/doctrine/annotations) 


### 链接
 <a href="https://github.com/HGthecode/apidoc-ui" target="_blank">ApiDoc UI前端</a>
 
 <a href="https://github.com/HGthecode/thinkphp-apidoc-demo" target="_blank">ApiDoc Demo 示例项目</a>
