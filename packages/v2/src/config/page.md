---
icon: config
category: 前端配置
# sidebarDepth: 2
# sidebar: auto
---

# 页面配置

::: tip
前端页面配置主要用于控制apidoc页面的配置，配置文件在 `public/apidoc/config.js`

默认配置文件请查看 [apidoc/config.js](https://github.com/HGthecode/apidoc-ui/blob/master/apidoc/config.js)
:::

## HOST
- 类型: string
- 默认值: 请求host

apidoc接口请求地址（非调试接口的地址）


## MENU
- 类型: object

接口菜单配置

### MENU.SHOW_CONTROLLER_CLASS
- 类型: boolean
- 默认值:true

是否显示控制器类名


### MENU.SHOW_API_URL
- 类型: boolean
- 默认值:true

是否显示接口url

### MENU.SHOW_API_METHOD
- 类型: boolean
- 默认值:true

是否显示接口请求类型

### HOSTS
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
    {
      title: "demo演示",
      host: "http://apidoc.demo.hg-code.com"
    }
]
```