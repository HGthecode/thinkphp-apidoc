<?php

namespace hg\apidoc;

use think\facade\Config;
use think\facade\Route;

class Service extends \think\Service
{

    public function boot()
    {

        $this->registerRoutes(function (){
            $route_prefix = 'apidoc';
            $apidocConfig = Config::get("apidoc")?Config::get("apidoc"):Config::get("apidoc.");
            $routes = function () {
                $controller_namespace = '\hg\apidoc\Controller@';
                Route::get('config'     , $controller_namespace . 'getConfig');
                Route::get('apiData'     , $controller_namespace . 'getApidoc');
                Route::get('mdDetail'     , $controller_namespace . 'getMdDetail');
                Route::post('verifyAuth'     , $controller_namespace . 'verifyAuth');
                Route::post('createCrud'     , $controller_namespace . 'createCrud');
            };
            if (!empty($apidocConfig['allowCrossDomain'])){
                Route::group($route_prefix, $routes)->allowCrossDomain();
            }
            Route::group($route_prefix, $routes);
        });


    }


}