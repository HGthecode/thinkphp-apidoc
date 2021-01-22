<?php
return [
    // 文档标题
    'title'              => 'APi接口文档',
    // 文档描述
    'desc'               => '',
    // 版权申明
    'copyright'          => 'Powered By hg-code',
    // 设置可选版本
    'versions'           => [
        ['title'=>'V1.0','folder'=>''],
    ],
    // 控制器分组
    'groups'             => [],
    // 指定公共注释定义的文件地址
    'definitions'        => "app\controller\Definitions",
    //指定生成文档的控制器
    'controllers'        => [],
    // 过滤，不解析的控制器
    'filter_controllers' => [],
    // 缓存配置
    'cache'              => [
        // 是否开启缓存
        'enable' => false,
        // 缓存文件路径
        'path'   =>  '../runtime/apidoc/',
        // 是否显示更新缓存按钮
        'reload' => true,
        // 最大缓存文件数
        'max'    => 5,  //最大缓存数量
    ],
    // 权限认证配置
    'auth'               => [
        // 是否启用密码验证
        'enable'     => false,
        // 验证密码
        'password'   => "123456",
        // 密码加密盐
        'secret_key' => "apidoc#hg_code",
    ],
    // 设置全局Authorize时请求头headers携带的key，对应token的key
    'global_auth_key'    => "Authorization",
    // 过滤、不解析的方法名称
    'filter_method'     => [
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
    // 统一的请求响应体，仅显示在文档提示中
    'responses'          => '{
    "code":"状态码",
    "message":"操作描述",
    "data":"业务数据",
}',
    // md文档
    'docs'              => [
        'menu_title' => '开发文档',
        'menus'      => []
    ]

];
