<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

//全局支持跨域
Route::allowCrossDomain([
    'Access-Control-Allow-Headers'   => 'Authorization,apidocToken, User-Agent, Keep-Alive, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With'
]);

Route::get('apidoc/config', "\\hg\\apidoc\\Controller@getConfig");
Route::get('apidoc/data', "\\hg\\apidoc\\Controller@getList");
Route::post('apidoc/auth', "\\hg\\apidoc\\Controller@verifyAuth");