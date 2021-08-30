

# 多语言

## 前端多语言

前端配置文件`/public/apidoc/config.js`中配置`LANG`，增加/修改你所需的语言，默认为简体中文，当配置大于1个语言时，页面右上角会出现语言切换功能

> messages 中为前端页面中所需的语言变量

```javascript
// /config.js
const config = {
   //...
   // 多语言
  LANG: [
    {
      title: "简体中文",
      lang: "zh-cn",
      messages: {
        "app.title": "Apidoc",
        "home.title": "首页",
        "home.appCount": "应用数",
        "home.apiCount": "API数量",
        "home.docsCount": "文档数量",
        "common.ok": "确认",
        "common.cancel": "取消",
        "common.clear": "清空",
        "common.desc": "说明",
        "common.action": "操作",
        "common.field": "字段",
        "common.type": "类型",
        "common.require": "必填",
        "common.defaultValue": "默认值",
        "common.value": "值",
        "common.api": "API",
        "common.docs": "文档",
        "common.close": "关闭",
        "common.view": "查看",
        "common.copySuccess": "复制成功",
        "common.page.404": "404-未知页面",
        "common.notdata": "暂无数据",
        "common.group": "分组",
        "common.currentApp": "当前应用",
        "lang.change.confirm.title": "您确认切换语言为 {langTitle} 吗？",
        "lang.change.confirm.content": "确认后将刷新页面，并回到首页",
        "host.change.confirm.title": "您确认切换Host为 {hostTitle} 吗？",
        "auth.title": "授权访问",
        "auth.input.placeholder": "请输入访问密码",
        "apiPage.update.tip": "该接口有更新",
        "apiPage.update.button": "点击此处更新",
        "apiPage.author": "作者",
        "apiPage.tag": "标签",
        "apiPage.docs": "文档",
        "apiPage.json": "Json",
        "apiPage.debug": "调试",
        "apiPage.title.header": "请求头Headers",
        "apiPage.title.params": "请求参数Parameters",
        "apiPage.title.responses": "响应结果Responses",
        "apiPage.mdDetail.title": "{name} 字段的说明",
        "apiPage.debug.mock.reload": "更新Mock",
        "apiPage.debug.excute": "执行 Excute",
        "layout.menu.reload": "更新菜单",
        "layout.menu.openAll": "展开全部",
        "layout.menu.hideAll": "收起全部",
        "layout.cache.reload": "更新缓存",
        "layout.tabs.leftSide": "左侧",
        "layout.tabs.rightSide": "右侧",
        "layout.tabs.notTab": "没有标签",
        "layout.tabs.closeCurrent": "关闭当前",
        "layout.tabs.closeLeft": "关闭左侧",
        "layout.tabs.closeRight": "关闭右侧",
        "layout.tabs.closeAll": "关闭全部",
        "globalParam.title": "全局参数",
        "globalParam.header": "Header",
        "globalParam.header.message": "发送请求时，所有接口将自动携带以下Header参数。",
        "globalParam.params": "Params",
        "globalParam.params.message": "发送请求时，所有接口将自动携带以下Params参数。",
        "globalParam.cancel.confirm": "确认清空以上所有参数吗?",
        "globalParam.add": "添加参数",
        "apiPage.json.formatError": "json 参数格式化错误",
        "apiPage.debug.event.before": "请求前事件",
        "apiPage.debug.event.after": "响应后事件",
        "apiPage.debug.event.setHeader": "设置请求头参数",
        "apiPage.debug.event.setGlobalHeader": "设置全局请求头参数",
        "apiPage.debug.event.setGlobalParam": "设置全局请求参数",
        "apiPage.debug.event.clearGlobalHeader": "清除全局请求头参数",
        "apiPage.debug.event.clearGlobalParam": "清除全局请求参数",
        "apiPage.debug.event.setParam": "设置请求参数",
        "apiPage.debug.event.clearParam": "清除请求参数",
        "apiPage.debug.event.handleParam": "处理请求参数",
      },
    },
    {
      title: "Engilsh",
      lang: "en-us",
      messages: {
          // ...
      }
    }
}
```

## 配置项多语言

如果的项目存在多种语言，希望在apidoc配置中也同样能进行多语言切换，可将apidoc.php配置文件进行如下调整

:::tip 提示
你的项目需按TP[多语言](https://www.kancloud.cn/manual/thinkphp6_0/1037637) 进行配置，并且对使用的变量进行定义
:::

### 定义TP多语言变量

```php
// /app/lang/zh-cn.php
return [
    'apidoc'    =>    [
        'apps.admin' => '后台管理',
        'group.base' => '基础模块',
        'headers.shopId' => '店铺id',
        'parameters.code' => '编码',
        'responses.code' => '响应编码',
        'responses.message' => '操作描述',
        'doc.about' => '关于',
        'api.lang.title' => "多语言接口",
        'api.lang.desc' => "多语言测试接口",
        'api.lang.age' => "年龄",
        'api.lang.name' => "姓名",
    ]
];
```
```php
// /app/lang/en-us.php
return [
    'apidoc'    =>    [
        'apps.admin' => 'Admin',
        'group.base' => 'Base Module',
        'headers.shopId' => 'Shop Id',
        'parameters.code' => 'Code',
        'responses.code' => 'Code',
        'responses.message' => 'Message',
        'doc.about' => 'About',
        'api.lang.title' => "Language Api",
        'api.lang.desc' => "This is a language Test Api",
        'api.lang.age' => "Age",
        'api.lang.name' => "Name",
    ]
];
```

### 配置文件中使用

> 如下例子中的参数位置可使用 `lang(多语言变量)` 来引用多语言变量

```php
// /config/apidoc.php
'apps' => [
    [
        'title'=>'lang(apidoc.apps.admin)',
        'path'=>'app\admin\controller',
        'folder'=>'admin',
        'groups'             => [
                ['title'=>'lang(apidoc.group.base)','name'=>'nouse'],
        ],
    ],
]
// 统一的请求Header
'headers'=>[
    ['name'=>'ShopId','type'=>'string','desc'=>'lang(apidoc.headers.shopId)'],
],
// 统一的请求参数Parameters
'parameters'=>[
    ['name'=>'code','type'=>'string','desc'=>'lang(apidoc.parameters.code)'],
],
// 统一的请求响应体
'responses'=>[
    ['name'=>'code','desc'=>'lang(apidoc.responses.code)','type'=>'int'],
    ['name'=>'message','desc'=>'lang(apidoc.responses.message)','type'=>'string'],
],
// md文档
'docs' => [
    ['title'=>'lang(apidoc.doc.about)','path'=>'docs/readme'],
],
```


## API注解多语言

如果的项目存在多种语言，希望在API接口的参数说明也能进行多语言切换，可对注解进行多语言变量引用

> 首先定义TP多语言变量，参考[定义TP多语言变量](#定义TP多语言变量)

```php
/**
 * @Apidoc\Title ("lang(apidoc.api.lang.title)")
 * @Apidoc\Desc ("lang(apidoc.api.lang.desc)")
 * @Apidoc\Param("age",type="int",desc="lang(apidoc.api.lang.age)")
 * @Apidoc\Returned("name",type="string",desc="lang(apidoc.api.lang.name)")
 */
public function lang(Request $request){
    //...
}
```


## 数据表字段描述

通常很多接口中的字段描述是通过ref解析模型对应的数据表注释来得到的，当我们希望apidoc读取数据表注释时，也能实现多语言切换，只需要在字段注释中加入`lang(xxx)`即可，如下：

```php
CREATE TABLE `user` (↵  
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` varchar(64) NOT NULL COMMENT '用户名，lang(apidoc.table.user.username)',
  `nickname` varchar(64) DEFAULT NULL COMMENT '昵称，lang(apidoc.table.user.nickname)',
  `password` char(64) NOT NULL COMMENT '登录密码，lang(apidoc.table.user.password)',
PRIMARY KEY (`id`)↵) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8"
```

如上例子中，字段注释中加入了`lang(xxx)`的内容，apidoc在解析该字段时，会使用多语言变量进行输出。



