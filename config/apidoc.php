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
    // 指定公共注释定义的文件地址
    'definitions'=>"app\controller\Definitions",
    // 设置可选版本
    'versions'=>[
        ['title'=>'V1.0','folder'=>'']
    ],
    // 是否开启缓存
    'with_cache'=>false,
    // 统一的请求响应体
    'responses'=>'{
    "code":"状态码",
    "message":"操作描述",
    "data":"业务数据",
    "timestamp":"响应时间戳"
}',
    // 设置全局Authorize时请求头headers携带的key
    'global_auth_key'=>"Authorization",
    // 密码验证配置
    'auth'=>[
        // 是否启用密码验证
        'with_auth'=>false,
        // 验证密码
        'auth_password'=>"123456",
        // 验证请求头中apidocToken的字段，默认即可
        'headers_key'=>"apidocToken",
    ],
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
