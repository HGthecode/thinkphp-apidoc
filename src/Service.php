<?php

namespace hg\apidoc;

use think\facade\Route;

class Service extends \think\Service
{

    public function boot()
    {
        // TODO apidocToken 跨域导致config报错
        $this->registerRoutes(function (){
            $route_prefix = 'apidoc';
            Route::group($route_prefix, function () {
                $controller_namespace = '\hg\apidoc\Controller@';
                Route::get('config'     , $controller_namespace . 'getConfig')->allowCrossDomain();
                Route::get('apiData'     , $controller_namespace . 'getApidoc')->allowCrossDomain();
                Route::get('mdDetail'     , $controller_namespace . 'getMdDetail')->allowCrossDomain();
                Route::post('verifyAuth'     , $controller_namespace . 'verifyAuth')->allowCrossDomain();
                Route::post('createCrud'     , $controller_namespace . 'createCrud')->allowCrossDomain();
            });
        });


    }


}