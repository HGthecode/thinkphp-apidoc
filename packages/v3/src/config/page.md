---
icon: config
category: 前端配置
# sidebarDepth: 2
# sidebar: auto
---

# 前端配置

::: tip
主要用于控制apidoc页面的配置，配置文件在 `public/apidoc/config.js`

默认配置文件请查看 [apidoc/config.js](https://github.com/HGthecode/apidoc-ui/blob/master/apidoc/config.js)
:::

## TITLE
- 类型: string
- 默认值: 标题

项目标题



## CACHE
- 类型: object

### CACHE.PREFIX
- 类型: string
- 默认值:apidoc_

前端缓存前缀


## HTTP
- 类型: object

请求配置

### HTTP.TIMEOUT
- 类型: number
- 默认值:30000

请求超时时间（毫秒）



### HTTP.HOSTS
- 类型: array
- 默认值:undefined

配置多个可选的tp项目地址，可通过文档头部HOST选择框进行切换不同的项目文档

```json
HOSTS: [
    {
      title: "tp6本地",
      host: "http://tp6.apidoc.net.cn"
    },
    {
      title: "tp5本地",
      host: "http://tp5.apidoc.net.cn"
    },
]
```


### HTTP.HEADERS_ENCODEURICOMPONENT
- 类型: boolean
- 默认值:undefined

是否将请求头参数进行 encodeURIComponent 转码

## MENU
- 类型: object


### MENU.SHOWURL
- 类型: boolean
- 默认值:false

Api菜单是否显示url

### MENU.WIDTH
- 类型: number
- 默认值:300

左侧菜单默认宽度


### METHODCOLOR
- 类型: object

配置请求类型的显示颜色，如下

```js
// 请求类型的颜色
METHODCOLOR: {
  GET: "#87d068",
  POST: "#2db7f5",
  PUT: "#ff9800",
  DELETE: "#ff4d4f",
  PATCH: "#802feb",
}
```

### WITHCREDENTIALS
- 类型: boolean
- 默认值:false

接口调试时是否自动携带cookie


## LANG

- 类型: array

多语言配置，默认只有简体中文，如多个语言，可复制简体中文，修改为你所需的语言变量

```javascript
// /config.js
const config = {
   // 多语言
  LANG: [
    {
      title: "简体中文",
      lang: "zh-cn",
      messages: {
        "app.title": "Apidoc",
        "home.title": "首页",
        // ...
      }
    },
}
```




