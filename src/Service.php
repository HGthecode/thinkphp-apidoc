<?php

namespace hg\apidoc;

use think\facade\Config;
use think\facade\Route;

class Service extends \think\Service
{

    public function boot()
    {

        $this->registerRoutes(function (){
            $apidocConfig = Config::get("apidoc")?Config::get("apidoc"):Config::get("apidoc.");
            $route_prefix = 'apidoc';
            $routes = function () {
                $controller_namespace = '\hg\apidoc\Controller@';
                Route::get('config'     , $controller_namespace . 'getConfig');
                Route::get('apiData'     , $controller_namespace . 'getApidoc');
                Route::get('mdMenus'     , $controller_namespace . 'getMdMenus');
                Route::get('mdDetail'     , $controller_namespace . 'getMdDetail');
                Route::post('verifyAuth'     , $controller_namespace . 'verifyAuth');
                Route::post('generator'     , $controller_namespace . 'createGenerator');
            };
            if (!empty($apidocConfig['allowCrossDomain'])){
                Route::group($route_prefix, $routes)->allowCrossDomain();
            }else{
                Route::group($route_prefix, $routes);
            }
        });

    }


}