---
icon: log
sidebarDepth: 2
category: 指南
---

# 更新日志

::: tip 发布周期
- 主版本号：含有破坏性更新和新特性，不在发布周期内。
- 次版本号：带有新特性的向下兼容的版本。
- 修订版本号：日常 bugfix 更新
:::

## v2.4.2
`2020-04-01`

> 需升级前端文件 [v1.2.1](https://github.com/HGthecode/apidoc-ui/releases/download/v1.2.1/apidoc.zip)


- 支持int|boolean|date|datetime|time类型的字段json展示时自动生成相应值
- 增加url以/开头的容错处理
- 修正控制器无任何注解时的报错问题
- 修正json格式化对其问题

## v2.4.1
`2020-03-22`

> 无需升级前端文件

- 修正数据表字段有小数长度，解析异常的问题

## v2.4.0
`2020-03-18`

> 需升级前端文件 [v1.2.0](https://github.com/HGthecode/apidoc-ui/releases/download/v1.2.0/apidoc.zip)

- 文档页面支持设置全局请求Header、Params参数
- 新增配置`apidoc.headers`来设置全局请求头参数
- 新增配置`apidoc.parameters`来设置全局请求参数
- 增加自动将配置项`apidoc.definitions`通用注释文件排除在接口控制器外
- 接口描述Desc及Param、Returned子参数desc支持换行显示
- 调整统一响应体无配置mian字段时，合并返回Returned参数
- 调整统一响应体配置方式，可去除`responses.show_responses` `responses.data` 直接用`responses`配置为参数数组
- Param、Returned子参数`type`增加`float`、`date`、`time`、`datetime`类型
- 修正统一响应体在无配置Returned时不返回的问题
- 修正apps配置多个应用/版本存在相同`folder`时文档读取错误的问题
- 去除无用的解析注解时的缓存机制


## v2.3.0
`2020-03-16`

- 支持方法内多层数据结构的注解


## v2.2.2
`2020-03-12`

- 支持控制器方法添加`NotDefaultAuthor`注释,不使用默认作者
- 支持控制器方法添加`NotResponses`注释,不使用统一响应体返回数据
- 控制器分组不显示当前应用/版本无数据的选项


## v2.2.1
`2020-03-11`

- 支持控制器\接口标题直接写
- 支持控制器\控制器方法添加`NotParse`注释排除该方法的解析


## v2.2.0
`2020-02-25`

- 支持tags筛选
- 支持配置默认请求类型
- 支持配置默认作者名称
- 修正接口调试，header参数默认值自动填入
- 修正控制器接口自动生成不准确问题


## v2.1.3
`2020-02-22`

- 修正filter_controllers过滤控制器无效问题

## v2.1.2
`2020-02-14`

- 修正Crud输入的文件命名规范验证



## v2.1.1
`2020-02-11`

- 新增可视化一键Crud
- 全新的多应用、多版本管理
- 优化支持Responses统一响应体返回配置，[responses配置](/v2/config/#responses)
- 支持配置读取数据表字段时自动转驼峰
- 修正tag显示问题


## v2.0.5
`2020-02-05`

- 修正解析控制器时，无注解的方法也被解析的问题

## v2.0.4
`2020-02-03`

- 修正模型use可能引发的异常
- 支持接口返回非json格式的展示

## v2.0.3
`2020-02-02`

- 修正header参数定义type无效问题
- 修正debug为false时注解缓存问题
## v2.0.2
`2020-02-01`

- header支持ref引入
- ref引入支持深层引入
- 优化显示

## v2.0.1
`2020-01-23`

- 修正无注解时可能出现的异常
- 修正自动生成接口显示问题
- 修正控制器过滤问题

## v2.0.0
`2020-01-22`
完全重构版本，不向下兼容 `V1.x`

- 支持Markdown文档
- 规范配置文件的配置项
- 支持官方注解路由生成文档参数
- 使用[doctrine/annotations](https://github.com/doctrine/annotations)更规范的注解
- 优化文档页面效果

## v1.x
`2020-01-01`

查看 [V1.x版本文档](/v1/)




