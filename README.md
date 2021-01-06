<p align="center">
  <a href="#">
    <img width="120" src="https://hgthecode.github.io/thinkphp-apidoc/images/logo.png">
  </a>
</p>

<h1 align="center">
  <a target="_blank">ThinkPHP ApiDoc</a>
</h1>

<div align="center">

基于ThinkPHP6 根据注释自动生成API接口文档

</div>


### 文档
<a href="https://hgthecode.github.io/thinkphp-apidoc/">ThinkPHP ApiDoc 文档</a>

### 特性

- 开箱即用，安装后按文档编写注释即可。
- 版本管理，可生成不同版本的API接口文档，任意切换。
- 多应用、单应用均兼容。
- 丰富的公共注释定义、业务逻辑层、模型的引用。
- 可根据引用模型，获取数据表字段注释生成参数文档。
- 支持配置，API文档页访问需密码验证。
- 支持在线调试，及设置全局请求头Authorize，接口调试省时省力。
- 支持缓存接口数据，无需每次访问都生成一次数据，更可随时切换。
- 完善的文档及使用示例。

### 兼容

- ThinkPHP 5.x 
- ThinkPHP 6.x 

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
 2、下载完成后，将dist目录放到你的项目public目录下，并将dist文件夹名，重命名为`apidoc`

安装插件完成后 打开浏览器访问   http://你的域名/apidoc/ ，出现接口文档页面，表示安装成功


### 支持我们
如果本项目对您有所帮助，请点个Star支持我们

### 鸣谢
[ThinkPHP](http://www.thinkphp.cn/) 

### 链接
 <a href="https://github.com/HGthecode/apidoc-ui" target="_blank">ApiDoc UI前端</a>
 
 <a href="https://github.com/HGthecode/thinkphp-apidoc-demo" target="_blank">ApiDoc Demo 示例项目</a>

