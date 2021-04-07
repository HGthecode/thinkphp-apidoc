<?php

namespace hg\apidoc;

use think\facade\Route;

class Service extends \think\Service
{

    public function boot()
    {

        $this->registerRoutes(function (){
            $route_prefix = 'apidoc';
            Route::group($route_prefix, function () {
                $controller_namespace = '\hg\apidoc\Controller@';
                Route::get('config'     , $controller_namespace . 'getConfig');
                Route::get('data' , $controller_namespace . 'getData');
                Route::post('auth'  , $controller_namespace . 'verifyAuth');
                Route::post('crud'  , $controller_namespace . 'createCrud');
            });
        });

    }


}