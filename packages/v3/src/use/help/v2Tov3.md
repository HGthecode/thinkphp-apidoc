# V2.x升级V3.x指南

由于V3.0是不向下兼容的重构版本，如从V2.x升级到V3.0可参考本文进行升级

## 升级扩展至3.0

项目根目录执行

```sh
composer update hg/apidoc
```

## 更新前端文件

前往下载最新版本前端文件：[Apidoc UI v2.x](/guide/install/#添加前端页面)


## 调整配置

:::tip 提示
由于之前安装过2.x生成过该配置文件了，升级后该配置文件不会重新生成

可将 `/vendor/hg/apidoc/src/config.php` 的内容拷贝到原`config/apidoc.php`中，并参考[配置参数](/config/)进行配置
:::

主要调整配置有如下几点：

### apps的配置

v2.x中全局配置的`groups`与`controllers`；在v3.x版本中，归属到指定应用/版本中，结构更清晰。如下例子

<CodeGroup>

<CodeGroupItem title="v3.x" active>

```php
'apps' => [
    [
        'title'=>'后台管理',
        'path'=>'app\admin\controller',
        'folder'=>'admin',
        'groups'  => [
            ['title'=>'基础模块','name'=>'base'],
            ['title'=>'示例模块','name'=>'demo'],
            ['title'=>'多级模块','name'=>'subMenu',
                'children'=>[
                    ['title'=>'多级v1','name'=>'subv1',],
                    ['title'=>'多级v2','name'=>'subv2'],
                ]
            ],
        ],
        'controllers'=>[
            'app\admin\controller\BaseDemo',
            'app\admin\controller\CrudDemo',
        ]
    ],
]
```
</CodeGroupItem>
<CodeGroupItem title="v2.x">

```php
// 多应用/多版本
'apps' => [
    ['title'=>'后台管理','path'=>'app\admin\controller','folder'=>'admin'],
    //...
],
//生成文档的控制器
'controllers' => [
    'app\admin\controller\BaseDemo',
    'app\admin\controller\CrudDemo',
    ...
],
//设置控制器分组
'groups'=>[
    ['title'=>'基础模块','name'=>'base'],
    ['title'=>'示例模块','name'=>'demo'],
],
```
</CodeGroupItem>

</CodeGroup>


### docs的配置

v2.x中通过`docs.menu_title`指定文档目录标题，与`docs.menus`配置文档菜单；在v3.x版本中，只需直接通过二维数组定义文档菜单即可

<CodeGroup>

<CodeGroupItem title="v3.x" active>

```php
// markdown 文档
'docs' => [
    ['title'=>'md语法示例','path'=>'docs/Use'],
    //...
]
```
</CodeGroupItem>
<CodeGroupItem title="v2.x">

```php
// markdown 文档
'docs' => [
    'menu_title' => '开发文档',
    'menus'      => [
        ['title'=>'md语法示例','path'=>'docs/Use'],
        //...
    ]
]
```
</CodeGroupItem>

</CodeGroup>


### 废除的配置项
以下参数为v2.x版本中的配置参数，在v3.x版本中均已废除

|配置项|说明|
|-|-|
|copyright|文档首页显示的版权信息|
|groups|控制器分组；已移到apps中配置，参考[apps的配置](#apps的配置)|
|controllers|解析指定的控制器；已移到apps中配置，参考[apps的配置](#apps的配置)|
|filter_controllers|过滤不解析的控制器；已改为无任何apidoc注解就不会解析，或添加`NotParse`注释|
|cache.path|缓存存储路径；已改为tp自带缓存，无需配置|
|cache.max|缓存存储最大数量；已改为tp自带缓存，无需配置|
|controller_auto_search|多级路由自动生成url的分割符，已改为通过[配置auto_url](/config/#auto-url)实现|
|auto_url_rule|自动生成url的首字母规则，已改为通过[配置auto_url](/config/#auto-url)实现|
|docs.menu_title|文档菜单标题；docs配置参考[docs的配置](#docs的配置)|
|docs.menus|文档菜单；docs配置参考[docs的配置](#docs的配置)|


