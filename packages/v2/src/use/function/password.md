

# 密码验证

## 全局密码

配置文件`/config/apidoc.php`中的 auth 设置如下，即可在访问文档页面时需输入密码访问：

```php
// /config/apidoc.php
// 权限认证配置
'auth' => [
    // 是否启用密码验证
    'enable'     => true,
    // 验证密码
    'password'   => "123456",
    // 密码加密盐
    'secret_key' => "apidoc#hg_code",
],
```

## 应用/版本独立密码

config/apidoc.php配置文件将指定应用/版本，设置`password`字段，便可开启该应用的访问密码

```php
// /config/apidoc.php

'apps'           => [
    ['title'=>'后台管理','path'=>'app\admin\controller','folder'=>'admin','password'=>'123'],
    [
        'title'=>'演示示例',
        'folder'=>'demo',
        'items'=>[
            ['title'=>'V1.0','path'=>'app\demo\controller\v1','folder'=>'v1'],
            ['title'=>'V2.0','path'=>'app\demo\controller\v2','folder'=>'v2','password'=>'456']
        ]
    ]
],
```

如上配置，访问`admin`应用需要使用`123`进行密码校验；访问`demo 的v2`时需要`456`进行密码校验

