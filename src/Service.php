<?php

namespace hg\apidoc;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use think\App;
use think\Cache;
use think\Config;
use think\facade\Route;

class Service extends \think\Service
{

    public function boot()
    {

        $this->registerRoutes(function (Config $config){

            AnnotationReader::addGlobalIgnoredName('mixin');

            // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
            AnnotationRegistry::registerLoader('class_exists');

            $this->app->bind(Reader::class, function (App $app, Config $config, Cache $cache) {
                $store = $config->get('apidoc.cache.store',null);
                $debug = $config->get('apidoc.cache.enable')==true?false:true;
                return new CachedReader(new AnnotationReader(), $cache->store($store), $debug);
            });

            $route_prefix = 'apidoc';
            Route::group($route_prefix, function () {
                $controller_namespace = '\hg\apidoc\Controller@';
                Route::get('config'     , $controller_namespace . 'getConfig');
                Route::get('data' , $controller_namespace . 'getData');
                Route::post('auth'  , $controller_namespace . 'verifyAuth');
            });
        });

    }


}