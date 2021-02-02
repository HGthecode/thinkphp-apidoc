<?php
namespace hg\apidoc;


use ReflectionClass;
use think\annotation\route\Resource;
use think\annotation\Route;
use hg\apidoc\annotation\Group;
use hg\apidoc\annotation\Param;
use hg\apidoc\annotation\Title;
use hg\apidoc\annotation\Desc;
use hg\apidoc\annotation\Author;
use hg\apidoc\annotation\Tag;
use hg\apidoc\annotation\Header;
use hg\apidoc\annotation\Returned;
use hg\apidoc\annotation\ParamType;
use hg\apidoc\annotation\Url;
use hg\apidoc\annotation\Method;
use think\annotation\route\Group as RouteGroup;

trait ParseAnnotation
{

    protected function parseController($class)
    {
        $config = $this->app->config->get('apidoc');
        $data=[];
        $refClass = new ReflectionClass($class);
        $title = $this->reader->getClassAnnotation($refClass,Title::class);
        $group = $this->reader->getClassAnnotation($refClass,Group::class);

        $routeGroup = $this->reader->getClassAnnotation($refClass,RouteGroup::class);
        $controllersNameArr = explode("\\", $class);
        $controllersName = $controllersNameArr[count($controllersNameArr)-1];
        $data['controller']=$controllersName;
        $data['title'] = !empty($title) && !empty($title->value) ? $title->value : $controllersName;
        $data['group'] = !empty($group->value)?$group->value:null;
        $methodList = [];
        $filter_method = !empty($config['filter_method'])?$config['filter_method']:[];

        foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
            if (!empty($refMethod->name) && !in_array($refMethod->name, $filter_method)){
                $methodItem= $this->parseAnnotation($refMethod,true);
                if (empty($methodItem['method'])){
                    $methodItem['method']='GET';
                }
                if (!empty($methodItem) && !empty($methodItem['url'])){
                    // 路由分组，url加上分组
                    if (!empty($routeGroup->value)){
                        $methodItem['url'] = $routeGroup->value.'/'.$methodItem['url'];
                    }
                    $methodList[]=$methodItem;
                }else if(empty($methodItem['url'])){
                    // 无url,自动生成
                    $methodItem['url'] = $this->autoCreateUrl($refMethod);
                    $methodList[]=$methodItem;
                }
            }

        }
        $data['children']=$methodList;
        return $data;
    }

    /**
     * 自动生成url
     * @param $method
     * @return string
     */
    protected function autoCreateUrl($method){
        // 控制器地址
        $class = $method->class;
        $controller_layer = $this->app->config->get('route.controller_layer');
        if(strpos($class,$controller_layer)!==false) {
            $pathArr = explode($controller_layer, $class);
            $classPath = $pathArr[1];
            $classPath = str_replace("\\","/",$classPath);
        }else{
            $pathArr = explode("\\", $class);
            $classPath = $pathArr[count($pathArr)-1];

        }
        return $classPath.'/'.$method->name;
    }

    protected function renderRef($refPath,$enableRefService=true){
        $res = ['type'=>'model'];
        // 通用定义引入
        if(strpos($refPath,'\\')===false){
            $config = $this->app->config->get('apidoc');
            $refPath = $config['definitions'].'\\'.$refPath;
            $data = $this->renderService($refPath);
            $res['type']="service";
            $res['data']=$data;
            return $res;
        }
        // 模型引入
        $modelData = (new ParseModel($this->reader))->renderModel($refPath);
        if ($modelData !==false){
            $res['data']=$modelData;
            return $res;
        }
        if ($enableRefService===false){
            return false;
        }
        $data = $this->renderService($refPath);
        $res['type']="service";
        $res['data']=$data;
        return $res;
    }
    
    protected function renderService($refPath){
        $pathArr = explode("\\", $refPath);
        $methodName = $pathArr[count($pathArr)-1];
        unset($pathArr[count($pathArr)-1]);
        $classPath = implode("\\", $pathArr);
        $classReflect = new \ReflectionClass($classPath);
        $methodName = trim ( $methodName );
        $refMethod = $classReflect->getMethod($methodName);
        $res = $this->parseAnnotation($refMethod,true);
        return $res;
    }

    /**
     * 解析方法注释
     * @param $refMethod
     * @param bool $enableRefService 是否终止service的引入
     * @return array
     */
    protected function parseAnnotation($refMethod,$enableRefService = true){
        $data=[];
        if ($annotations = $this->reader->getMethodAnnotations($refMethod)) {
            $headers=[];
            $params = [];
            $returns = [];

            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof Param:
                        if (!empty($annotation->ref)){
                            $refRes = $this->renderRef($annotation->ref,$enableRefService);
                            $params = $this->handleRefData($params,$refRes,$annotation,'param');
                        }else{
                            $param=[
                                "name"=>$annotation->value,
                                "type"=>$annotation->type,
                                "desc"=>$annotation->desc,
                                "default"=>$annotation->default,
                                "require"=>$annotation->require,
                            ];
                            $params[] = $param;
                            
                        }
                        break;
                    case $annotation instanceof Returned:
                        if (!empty($annotation->ref)){
                            $refRes = $this->renderRef($annotation->ref,$enableRefService);
                            $returns = $this->handleRefData($returns,$refRes,$annotation,'return');
                        }else{
                            $param=[
                                "name"=>$annotation->value,
                                "type"=>$annotation->type,
                                "desc"=>$annotation->desc,
                                "default"=>$annotation->default,
                                "require"=>$annotation->require,
                            ];
                            $returns[] = $param;
                        }
                        break;
                    case $annotation instanceof Header:
                        if (!empty($annotation->ref)){
                            $refRes = $this->renderRef($annotation->ref,$enableRefService);
                            $headers = $this->handleRefData($headers,$refRes,$annotation,'header');
                        }else {
                            $param = [
                                "name" => $annotation->value,
                                "desc" => $annotation->desc,
                                "require" => $annotation->require,
                                "type"=>$annotation->type,
                                "default"=>$annotation->default,
                            ];
                            $headers[] = $param;
                        }
                        break;
                    case $annotation instanceof Route:
                        if (empty($data['method'])){
                            $data['method'] = $annotation->method;
                        }
                        if (empty($data['url'])){
                            $data['url']=$annotation->value;
                        }
                        break;
                    case $annotation instanceof Author:
                        $data['author']=$annotation->value;
                        break;

                    case $annotation instanceof Title:
                        $data['title']=$annotation->value;
                        break;
                    case $annotation instanceof Desc:
                        $data['desc']=$annotation->value;
                        break;
                    case $annotation instanceof ParamType:
                        $data['paramType']=$annotation->value;
                        break;
                    case $annotation instanceof Url:
                        $data['url']=$annotation->value;
                        break;
                    case $annotation instanceof Method:
                        $data['method']=$annotation->value;
                        break;
                    case $annotation instanceof Tag:
                        $data['tag']=$annotation->value;
                        break;
                }
            }
            $data['header']=$headers;
            $data['param']=$params;
            $data['return']=$returns;
        }
        return $data;
    }


    /**
     * 处理param、returned 参数
     * @param $params
     * @param $refRes
     * @param $annotation
     * @return array
     */
    protected function handleRefData($params,$refRes,$annotation,$field){
        if ($refRes['type']==="model" && count($refRes['data'])>0){
            // 模型引入
            $data = $refRes['data'];
        }else if($refRes['type']==="service" && !empty($refRes['data']) && !empty($refRes['data'][$field]) ){
            // service引入
            $data=$refRes['data'][$field];
        }else{
            return $params;
        }
        // 过滤field
        if (!empty($annotation->field)){
            $data=(new Utils())->filterParamsField($data,$annotation->field,'field');
        }
        // 过滤withoutField
        if (!empty($annotation->withoutField)){
            $data=(new Utils())->filterParamsField($data,$annotation->withoutField,'withoutField');
        }
        
        if (!empty($annotation->value)){
            $item = [
                'name'=>$annotation->value,
                'desc'=>$annotation->desc,
                'type'=>$annotation->type,
                'require'=>$annotation->require,
                'default'=>$annotation->default,
                'params'=>$data
            ];
            if ($annotation->type === 'tree'){
                // 类型为tree的
                $item['params'][]= [
                    'params'=>$data,
                    'name'=>$annotation->childrenField,
                    'type'=>'array',
                    'desc'=>$annotation->childrenDesc,
                ];
                $params[]=$item;
            }else{
                $params[] = $item;
            }
        }else{
            $params = array_merge($params,$data);
        }
        return $params;
    }
}