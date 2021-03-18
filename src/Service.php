<?php

namespace hg\apidoc;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use think\facade\Route;

class Service extends \think\Service
{

    public function boot()
    {

        $this->registerRoutes(function (){

            AnnotationReader::addGlobalIgnoredName('mixin');

            // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
            AnnotationRegistry::registerLoader('class_exists');

            $this->app->bind(Reader::class, function () {
                return new AnnotationReader();
            });

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