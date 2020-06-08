<?php
return [
    // 文档标题
    'title'=>'APi接口文档',
    // 版权申明
    'copyright'=>'Powered By HG',
    //生成文档的控制器
    'controllers' => [
        // 'api\\controller\\ApiTest',
    ],
    // 设置可选版本
    'versions'=>[
        ['title'=>'V1.0','folder'=>'']
    ],
    // 是否开启缓存，开启后，如存在缓存数据优先取缓存数据，需手动更新接口参数，关闭则每次刷新重新生成接口数据
    'with_cache'=>false,
    // 统一的请求响应体
    'responses'=>'{
    "code":"状态码",
    "message":"操作描述",
    "data":"业务数据",
    "timestamp":"响应时间戳"
}',
    // 设置全局Authorize时请求头headers携带的key，对应token的key
    'global_auth_key'=>"Authorization",
    // 权限认证配置
    'auth'=>[
        // 是否启用权限认证，启用则需登录
        'with_auth'=>false,
        // 验证类型为 password 时验证的密码，前端md5回传
        'auth_password'=>"123456",
        // 验证请求头中apidocToken的字段
        'headers_key'=>"apidocToken",
    ],
    // 指定公共注释定义的文件地址
    'definitions'=>"app\controller\Definitions",
    // 过滤、不解析的方法名称
    'filter_method'=>[
        '_empty',
        '_initialize',
        '__construct',
        '__destruct',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__cal',
    ],
];
