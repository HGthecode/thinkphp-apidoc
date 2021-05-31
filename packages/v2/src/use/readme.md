# 建议及规范

## 建议
- 如果你使用PHPStorm的话，建议安装PHP [Annotations插件](https://plugins.jetbrains.com/plugin/7320-php-annotations)，可以支持注解的语法提示及自动完成

- 配合查看[演示项目](https://apidoc.demo.hg-code.com/apidoc/?appKey=admin)与[演示源码](https://github.com/HGthecode/thinkphp-apidoc-demo)上手更快哦！



## 书写规范
::: warning 书写参数时有如下几个规范
- 控制器必须`use`引入注释解释文件
- 每个参数以 @+参数名("参数值",子参数名="子参数值",...)
- 子参数需用双引号包裹
:::
