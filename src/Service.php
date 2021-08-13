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
                Route::get('mdMenus'     , $controller_namespace . 'getMdMenus');
                Route::get('mdDetail'     , $controller_namespace . 'getMdDetail');
                Route::post('verifyAuth'     , $controller_namespace . 'verifyAuth');
            };
            if (!empty($apidocConfig['allowCrossDomain'])){
                Route::group($route_prefix, $routes)->allowCrossDomain();
            }else{
                Route::group($route_prefix, $routes);
            }
        });

        // 合并默认配置
        $config = Config::get("apidoc")?Config::get("apidoc"):Config::get("apidoc.");
        if (!(!empty($config['apps']) && count($config['apps']))){
            $default_app = Config::get("app.default_app");
            $namespace = \think\facade\App::getNamespace();
            $defaultAppConfig = ['title'=>$default_app,'path'=>$namespace.'\\'.$default_app.'\\controller','folder'=>$default_app];
            $config['apps'] = [$defaultAppConfig];
        }
        Config::set(['apidoc'=>$config]);


    }


}