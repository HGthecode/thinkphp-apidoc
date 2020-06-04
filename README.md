<p align="center">
  <a href="#">
    <img width="200" src="https://cdn.nlark.com/yuque/0/2020/png/920342/1590716607655-faf3ad2d-e36a-4fcc-9d01-7c76259739c5.png">
  </a>
</p>

<h1 align="center">
  <a href="#" target="_blank">ThinkPHP6 ApiDoc</a>
</h1>

<div align="center">

基于ThinkPHP6 根据注释自动生成API接口文档

</div>

<p align="center">
    <img width="300" src="https://cdn.nlark.com/yuque/0/2020/jpeg/920342/1590718471948-ffe9bea0-cade-4880-a39a-8ae36a647f5d.jpeg">
    <img width="300" src="https://cdn.nlark.com/yuque/0/2020/jpeg/920342/1590718511617-1534b6a7-1261-44a6-804b-65482ed764c9.jpeg">
    <img width="300" src="https://cdn.nlark.com/yuque/0/2020/jpeg/920342/1590718533577-6d744090-fdae-4132-92ed-ac3d930f425b.jpeg">
</p>


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

### 支持环境

- ThinkPHP 6.x 
- PHP >= 7.1.0 （使用ThinkPHP 6 的基本要求）

### 安装
1、安装插件：进入ThinkPHP6 项目根目录，执行如下命令：
```bash
$ composer require hg/apidoc-tp6 
```

2、安装前端页面：执行以下命令下载UI。
```bash
$ git clone https://github.com/HGthecode/apidoc-ui.git
```
下载完成后，找到dist目录，将该目录放到你的项目public目录下，并将dist文件夹名，重命名为 apidoc


3、浏览器访问文档页面：
 http://你的域名/apidoc/

### 链接
 <a href="https://github.com/HGthecode/apidoc-ui" target="_blank">ApiDoc UI </a>



