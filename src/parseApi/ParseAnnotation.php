<?php
declare(strict_types = 1);

namespace hg\apidoc\parseApi;

use Doctrine\Common\Annotations\AnnotationReader;
use hg\apidoc\Utils;
use ReflectionClass;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use think\annotation\route\Resource;
use think\annotation\Route;
use hg\apidoc\annotation\Group;
use hg\apidoc\annotation\Sort;
use hg\apidoc\annotation\Param;
use hg\apidoc\annotation\Title;
use hg\apidoc\annotation\Desc;
use hg\apidoc\annotation\Md;
use hg\apidoc\annotation\Author;
use hg\apidoc\annotation\Tag;
use hg\apidoc\annotation\Header;
use hg\apidoc\annotation\Returned;
use hg\apidoc\annotation\ParamType;
use hg\apidoc\annotation\Url;
use hg\apidoc\annotation\Method;
use hg\apidoc\annotation\Before;
use hg\apidoc\annotation\After;
use think\annotation\route\Group as RouteGroup;
use think\facade\App;
use think\facade\Config;

class ParseAnnotation
{

    protected $config = [];

    protected $reader;

    //tags，当前应用/版本所有的tag
    protected $tags = array();

    //groups,当前应用/版本的分组name
    protected $groups = array();

    protected $controller_layer = "";

    public function __construct()
    {
        $this->reader = new AnnotationReader();
        $this->config = Config::get('apidoc')?Config::get('apidoc'):Config::get('apidoc.');
        $this->controller_layer = Config::get('route.controller_layer',"controller");
    }

    /**
     * 生成api接口数据
     * @param string $appKey
     * @return array
     */
    public function renderApiData(string $appKey): array
    {
        $currentApps = (new Utils())->getCurrentApps($appKey);
        $currentApp  = $currentApps[count($currentApps) - 1];

        if (!empty($currentApp['controllers']) && count($currentApp['controllers']) > 0) {
            // 配置的控制器列表
            $controllers = $this->getConfigControllers($currentApp['path'],$currentApp['controllers']);
        } else {
            // 默认读取所有的
            $controllers = $this->getDirControllers($currentApp['path']);
        }
        $apiData = [];
        if (!empty($controllers) && count($controllers) > 0) {
            foreach ($controllers as $class) {
                $classData = $this->parseController($class);
                if ($classData !== false) {
                    $apiData[] = $classData;
                }
            }
        }
        // 排序
        $apiList = Utils::arraySortByKey($apiData);
        $json = array(
            "data"   => $apiList,
            "tags"   => $this->tags,
            "groups" => $this->groups,
        );
        return $json;
    }

    /**
     * 获取生成文档的控制器列表
     * @param string $path
     * @return array
     */
    protected function getConfigControllers(string $path,$appControllers): array
    {
        $controllers = [];
        $configControllers = $appControllers;
        if (!empty($configControllers) && count($configControllers) > 0) {
            foreach ($configControllers as $item) {
                if ( strpos($item, $path) !== false && class_exists($item)) {
                    $controllers[] = $item;
                }
            }
        }
        return $controllers;
    }

    /**
     * 获取目录下的控制器列表
     * @param string $path
     * @return array
     */
    protected function getDirControllers(string $path): array
    {
        if ($path) {
            if (strpos(App::getRootPath(), '/') !== false) {
                $pathStr = str_replace("\\", "/", $path);
            } else {
                $pathStr = $path;
            }
            $dir = App::getRootPath() . $pathStr;
        } else {
            $dir = App::getRootPath() . $this->controller_layer;
        }
        $controllers = [];
        if (is_dir($dir)) {
            $controllers = $this->scanDir($dir, $path);
        }
        return $controllers;
    }

    /**
     * 处理指定目录下的控制器
     * @param string $dir
     * @param string $appPath
     * @return array
     */
    protected function scanDir(string $dir, string $appPath): array
    {
        $list = [];
        foreach (ClassMapGenerator::createMap($dir) as $class => $path) {
            if (strpos($class, $appPath) !== false) {
                $classNamespace = $class;
            }else{
                $pathStr = str_replace("/", "\\", $path);
                $pathArr   = explode($appPath, $pathStr);
                if (!empty($pathArr[1])){
                    $classNamespace = $appPath.str_replace(".php", "", $pathArr[1]);
                }else{
                    continue;
                }
            }
            if (
                !isset($this->config['filter_controllers']) ||
                (isset($this->config['filter_controllers']) && !in_array($classNamespace, $this->config['filter_controllers'])) &&
                $this->config['definitions'] != $classNamespace
            ) {
                if (strpos($classNamespace, '\\') === false) {
                    $list[] = $appPath . "\\" . $classNamespace;
                } else {
                    $list[] = $classNamespace;
                }
            }
        }
        return $list;
    }

    protected function parseController($class)
    {

        $data                 = [];
        $refClass             = new ReflectionClass($class);
        $classTextAnnotations = $this->parseTextAnnotation($refClass);
        if (in_array("NotParse", $classTextAnnotations)) {
            return false;
        }
        $title = $this->reader->getClassAnnotation($refClass, Title::class);
        $group = $this->reader->getClassAnnotation($refClass, Group::class);
        $sort = $this->reader->getClassAnnotation($refClass, Sort::class);

        $routeGroup         = $this->reader->getClassAnnotation($refClass, RouteGroup::class);
        $controllersNameArr = explode("\\", $class);
        $controllersName    = $controllersNameArr[count($controllersNameArr) - 1];
        $data['controller'] = $controllersName;
        $data['group']      = !empty($group->value) ? $group->value : null;
        $data['sort']      = !empty($sort->value) ? $sort->value : null;
        if (!empty($data['group']) && !in_array($data['group'], $this->groups)) {
            $this->groups[] = $data['group'];
        }
        $data['title'] = !empty($title) && !empty($title->value) ? $title->value : "";

        if (empty($title)) {
            if (!empty($classTextAnnotations) && count($classTextAnnotations) > 0) {
                $data['title'] = $classTextAnnotations[0];
            } else {
                $data['title'] = $controllersName;
            }
        }
        $data['title'] = Utils::getLang($data['title']);
        $methodList       = [];
        $data['menu_key'] = Utils::createRandKey($data['controller']);

        foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {

            $methodItem = $this->parseApiMethod($refMethod,$routeGroup);
            if ($methodItem===false){
                continue;
            }
            $methodList[] = $methodItem;

        }
        $data['children'] = $methodList;
        if (count($methodList)===0){
            return false;
        }
        return $data;
    }


    protected function parseApiMethod($refMethod,$routeGroup){
        $config               = $this->config;
        if (empty($refMethod->name)) {
            return false;
        }
        $methodItem = $this->parseAnnotation($refMethod, true,"controller");
        if (!count((array)$methodItem)) {
            return false;
        }
        $textAnnotations = $this->parseTextAnnotation($refMethod);
        // 标注不解析的方法
        if (in_array("NotParse", $textAnnotations)) {
            return false;
        }
        // 无标题，且有文本注释
        if (empty($methodItem['title']) && !empty($textAnnotations) && count($textAnnotations) > 0) {
            $methodItem['title'] = $textAnnotations[0];
        }
        // 添加统一headers请求头参数
        if (!empty($config['headers']) && !in_array("NotHeaders", $textAnnotations)) {
            $configHeaders = [];
            foreach ($config['headers'] as $headerItem){
                $headerItem['desc'] = Utils::getLang($headerItem['desc']);
                $configHeaders[] = $headerItem;
            }
            if (!empty($methodItem['header'])) {
                $methodItem['header'] = Utils::arrayMergeAndUnique("name", $configHeaders, $methodItem['header']);
            } else {
                $methodItem['header'] = $configHeaders;
            }
        }
        // 添加统一params请求参数
        if (!empty($config['parameters']) && !in_array("NotParameters", $textAnnotations)) {
            $configParams = [];
            foreach ($config['parameters'] as $paramItem){
                $paramItem['desc'] = Utils::getLang($paramItem['desc']);
                $configParams[] = $paramItem;
            }

            if (!empty($methodItem['param'])) {
                $methodItem['param'] = Utils::arrayMergeAndUnique("name", $configParams, $methodItem['param']);
            } else {
                $methodItem['param'] = $configParams;
            }
        }
        // 添加responses统一响应体
        if (
            !empty($config['responses']) &&
            !is_string($config['responses']) &&
            !in_array("NotResponses", $textAnnotations)
        ) {
            // 显示在响应体中
            $returned = [];
            $hasMian  = false;
            $responsesData = $config['responses'];
            // 合并统一响应体及响应参数相同的字段
            $returnData = [];
            $resKeys = [];
            foreach ($responsesData as $resItem) {
                $resKeys[]=$resItem['name'];
            }
            foreach ($methodItem['return'] as $returnItem){
                if (!(in_array($returnItem['name'],$resKeys) && $returnItem['source']==='controller' && !empty($returnItem['replaceGlobal']))){
                    $returnData[]=$returnItem;
                }
            }

            foreach ($responsesData as $resItem) {
                $resData = $resItem;
                $globalFind = Utils::getArrayFind($methodItem['return'],function ($item)use ($resItem){
                    if ($item['name'] == $resItem['name'] && $item['source']==='controller' && !empty($item['replaceGlobal'])){
                        return true;
                    }
                    return false;
                });
                if (!empty($globalFind)){
                    $resData = array_merge($resItem,$globalFind);
                }else if (!empty($resData['main']) && $resData['main'] === true) {
                    $resData['children'] = $returnData;

                    $resData['resKeys']=$resKeys;
                    $hasMian           = true;
                }
                $resData['find'] =$globalFind;
                $resData['desc'] = Utils::getLang($resData['desc']);
                $returned[] = $resData;
            }
            if (!$hasMian) {
                $returned = Utils::arrayMergeAndUnique("name", $returned, $methodItem['return']);
            }
            $methodItem['return'] = $returned;
        }
        // 默认method
        if (empty($methodItem['method'])) {
            $methodItem['method'] = !empty($config['default_method']) ? $config['default_method'] : 'GET';
        }
        $methodItem['method'] = strtoupper($methodItem['method']);


        // 默认default_author
        if (empty($methodItem['author']) && !empty($config['default_author']) && !in_array("NotDefaultAuthor", $textAnnotations)) {
            $methodItem['author'] = $config['default_author'];
        }

        // Tags
        if (!empty($methodItem['tag'])) {
            $tagText = Utils::getLang($methodItem['tag']);
            if (strpos($tagText, ',') !== false) {
                $tagArr = explode(",", $tagText);
                foreach ($tagArr as $tag) {
                    if (!in_array($tag, $this->tags)) {
                        $this->tags[] = $tag;
                    }
                }
                $methodItem['tag'] = $tagArr;
            } else {
                $methodItem['tag'] = [$tagText];
                if (!in_array($tagText, $this->tags)) {
                    $this->tags[] = $tagText;
                }
            }
        }

        // 无url,自动生成
        if (empty($methodItem['url'])) {
            $methodItem['url'] = $this->autoCreateUrl($refMethod);
        } else if (!empty($routeGroup->value)) {
            // 路由分组，url加上分组
            $methodItem['url'] = '/' . $routeGroup->value . '/' . $methodItem['url'];
        }else if (!empty($methodItem['url']) && substr($methodItem['url'], 0, 1) != "/") {
            $methodItem['url'] = "/" . $methodItem['url'];
        }
        $methodItem['name']     = $refMethod->name;
        $methodItem['menu_key'] = Utils::createRandKey($methodItem['method'] . "_" . $refMethod->name);
        return $methodItem;
    }

    /**
     * 自动生成url
     * @param $method
     * @return string
     */
    protected function autoCreateUrl($method): string
    {
        if (!empty($this->config['auto_url']) && !empty($this->config['auto_url']['custom']) && is_callable($this->config['auto_url']['custom'])){
           return $this->config['auto_url']['custom']($method->class,$method->name);
        }
        $searchString = $this->controller_layer . '\\';
        $substr = substr(strstr($method->class, $searchString), strlen($searchString));
        $multistage_route_separator = ".";
        if (!empty($this->config['auto_url']) && !empty($this->config['auto_url']['multistage_route_separator'])){
            $multistage_route_separator = $this->config['auto_url']['multistage_route_separator'];
        }
        $pathArr = explode("\\", str_replace($substr, str_replace('\\', $multistage_route_separator, $substr), $method->class));
        $filterPathNames = array(App::getNamespace(), $this->controller_layer);
        $classPathArr = [];
        foreach ($pathArr as $item) {
            if (!in_array($item, $filterPathNames)) {
                if (!empty($this->config['auto_url']) && !empty($this->config['auto_url']['letter_rule'])){
                    switch ($this->config['auto_url']['letter_rule']) {
                        case 'lcfirst':
                            $classPathArr[] = lcfirst($item);
                            break;
                        case 'ucfirst':
                            $classPathArr[] = ucfirst($item);
                            break;
                        default:
                            $classPathArr[] = $item;
                    }
                }else{
                    $classPathArr[] = $item;
                }
            }
        }
        $classPath = implode('/', $classPathArr);
        return '/' . $classPath . '/' . $method->name;
    }

    /**
     * ref引用
     * @param $refPath
     * @param bool $enableRefService
     * @return false|string[]
     */
    protected function renderRef(string $refPath, bool $enableRefService = true): array
    {
        $res = ['type' => 'model'];
        // 通用定义引入
        if (strpos($refPath, '\\') === false) {
            $config      = $this->config;
            $refPath     = $config['definitions'] . '\\' . $refPath;
            $data        = $this->renderService($refPath);
            $res['type'] = "service";
            $res['data'] = $data;
            return $res;
        }
        // 模型引入
        $modelData = (new ParseModel($this->reader))->renderModel($refPath);
        if ($modelData !== false) {
            $res['data'] = $modelData;
            return $res;
        }
        if ($enableRefService === false) {
            return false;
        }
        $data        = $this->renderService($refPath);
        $res['type'] = "service";
        $res['data'] = $data;
        return $res;
    }

    /**
     * 解析注释引用
     * @param $refPath
     * @return array
     * @throws \ReflectionException
     */
    protected function renderService(string $refPath)
    {
        $pathArr    = explode("\\", $refPath);
        $methodName = $pathArr[count($pathArr) - 1];
        unset($pathArr[count($pathArr) - 1]);
        $classPath    = implode("\\", $pathArr);
        $classReflect = new \ReflectionClass($classPath);
        $methodName   = trim($methodName);
        $refMethod    = $classReflect->getMethod($methodName);
        $res          = $this->parseAnnotation($refMethod, true);
        return $res;
    }

    /**
     * 处理Param/Returned的字段名name、params子级参数
     * @param $values
     * @return array
     */
    protected function handleParamValue($values, string $field = 'param'): array
    {
        $name   = "";
        $params = [];
        if (!empty($values) && is_array($values) && count($values) > 0) {
            foreach ($values as $item) {
                if (is_string($item)) {
                    $name = $item;
                } else if (is_object($item)) {
                    if (!empty($item->ref)) {
                        $refRes = $this->renderRef($item->ref, true);
                        $params = $this->handleRefData($params, $refRes, $item, $field);
                    } else {
                        $param         = [
                            "name"    => "",
                            "type"    => $item->type,
                            "desc"    => Utils::getLang($item->desc),
                            "default" => $item->default,
                            "require" => $item->require,
                            "childrenType"=> $item->childrenType
                        ];
                        $children      = $this->handleParamValue($item->value);
                        $param['name'] = $children['name'];
                        if (count($children['params']) > 0) {
                            $param['children'] = $children['params'];
                        }
                        $params[] = $param;
                    }
                }
            }
        } else {
            $name = $values;
        }
        return ['name' => $name, 'params' => $params];
    }

    /**
     * 解析方法注释
     * @param $refMethod
     * @param bool $enableRefService 是否终止service的引入
     * @param string $source 注解来源
     * @return array
     */
    protected function parseAnnotation($refMethod, bool $enableRefService = true,$source=""): array
    {
        $data = [];
        if ($annotations = $this->reader->getMethodAnnotations($refMethod)) {
            $headers = [];
            $params  = [];
            $returns = [];
            $before = [];
            $after = [];

            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof Param:
                        $params = $this->handleParamAndReturned($params,$annotation,'param',$enableRefService);
                        break;
                    case $annotation instanceof Returned:

                        $returns = $this->handleParamAndReturned($returns,$annotation,'return',$enableRefService,$source);
                        break;
                    case $annotation instanceof Header:
                        if (!empty($annotation->ref)) {
                            $refRes  = $this->renderRef($annotation->ref, $enableRefService);
                            $headers = $this->handleRefData($headers, $refRes, $annotation, 'header');
                        } else {
                            $param     = [
                                "name"    => $annotation->value,
                                "desc"    => Utils::getLang($annotation->desc),
                                "require" => $annotation->require,
                                "type"    => $annotation->type,
                                "default" => $annotation->default,
                            ];
                            $headers[] = $param;
                        }
                        break;
                    case $annotation instanceof Route:
                        if (empty($data['method'])) {
                            $data['method'] = $annotation->method;
                        }
                        if (empty($data['url'])) {
                            $data['url'] = $annotation->value;
                        }
                        break;
                    case $annotation instanceof Author:
                        $data['author'] = $annotation->value;
                        break;

                    case $annotation instanceof Title:
                        $data['title'] = Utils::getLang($annotation->value);
                        break;
                    case $annotation instanceof Desc:
                        $data['desc'] = Utils::getLang($annotation->value);
                        if (!empty($annotation->mdRef)){
                            $data['md'] = $annotation->mdRef;
                        }
                        break;
                    case $annotation instanceof Md:
                        $data['md'] = $annotation->value;
                        if (!empty($annotation->ref)){
                            $data['md'] = (new ParseMarkdown())->getContent("",$annotation->ref);
                        }
                        break;
                    case $annotation instanceof ParamType:
                        $data['paramType'] = $annotation->value;
                        break;
                    case $annotation instanceof Url:
                        $data['url'] = $annotation->value;
                        break;
                    case $annotation instanceof Method:
                        $data['method'] = $annotation->value;
                        break;
                    case $annotation instanceof Tag:
                        $data['tag'] = $annotation->value;
                        break;
                    case $annotation instanceof Before:
                        if (!empty($annotation->value) && is_array($annotation->value)){
                            $beforeInfo = Utils::objectToArray($annotation);
                            $valueList = [];
                            foreach ($annotation->value as $valueItem){
                                $valueItemInfo = Utils::objectToArray($valueItem);
                                if ($valueItem instanceof Before){
                                    $valueItemInfo['type'] = "before";
                                }else if ($valueItem instanceof After){
                                    $valueItemInfo['type'] = "after";
                                }
                                $valueList[] = $valueItemInfo;
                            }
                            $beforeInfo['value'] = $valueList;
                            $before[] = $beforeInfo;
                        }else{
                            $before[] = $annotation;
                        }

                        break;
                    case $annotation instanceof After:
                        if (!empty($annotation->value) && is_array($annotation->value)){
                            $afterInfo = Utils::objectToArray($annotation);
                            $valueList = [];
                            foreach ($annotation->value as $valueItem){
                                $valueItemInfo = Utils::objectToArray($valueItem);
                                if ($valueItem instanceof Before){
                                    $valueItemInfo['type'] = "before";
                                }else if ($valueItem instanceof After){
                                    $valueItemInfo['type'] = "after";
                                }
                                $valueList[] = $valueItemInfo;
                            }
                            $afterInfo['value'] = $valueList;
                            $after[] = $afterInfo;
                        }else{
                            $after[] = $annotation;
                        }
                        break;
                }
            }
            if ($headers && count($headers) > 0) {
                $data['header'] = $headers;
            }
            $data['param']  = $params;
            $data['return'] = $returns;
            $data['before'] = $before;
            $data['after'] = $after;
        }
        return $data;
    }


    /**
     * 处理请求参数与返回参数
     * @param $params
     * @param $annotation
     * @param string $type
     * @param false $enableRefService
     * @param string $source 注解来源
     * @return array
     */
    protected function handleParamAndReturned($params,$annotation,$type="param",$enableRefService=false,$source=""){
        if (!empty($annotation->ref)) {
            $refRes = $this->renderRef($annotation->ref, $enableRefService);
            $params = $this->handleRefData($params, $refRes, $annotation, $type,$source);
        } else {

            $param =  Utils::objectToArray($annotation);
            $param["source"] = $source;
            $param["desc"] = Utils::getLang($param['desc']);

            $children      = $this->handleParamValue($annotation->value, $type);
            $param['name'] = $children['name'];
            if (count($children['params']) > 0) {
                $param['children'] = $children['params'];

            }
            if ($annotation->type === 'tree' ) {
                // 类型为tree的
                $param['children'][] = [
                    'children' => $children['params'],
                    'name'   => !empty($annotation->childrenField)?$annotation->childrenField:"children",
                    'type'   => 'array',
                    'desc'   => Utils::getLang($annotation->childrenDesc),
                ];
            }
            // 合并同级已有的字段
            $params = Utils::arrayMergeAndUnique("name", $params, [$param]);
        }
            return $params;
    }

    /**
     * 解析非注解文本注释
     * @param $refMethod
     * @return array|false
     */
    protected function parseTextAnnotation($refMethod): array
    {
        $annotation = $refMethod->getDocComment();
        if (empty($annotation)) {
            return [];
        }
        if (preg_match('#^/\*\*(.*)\*/#s', $annotation, $comment) === false)
            return [];
        $comment = trim($comment [1]);
        if (preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false)
            return [];
        $data = [];
        foreach ($lines[1] as $line) {
            $line = trim($line);
            if (!empty ($line) && strpos($line, '@') !== 0) {
                $data[] = $line;
            }
        }
        return $data;
    }


    /**
     * 处理param、returned 参数
     * @param $params
     * @param $refRes
     * @param $annotation
     * @param string|null $source 注解来源
     * @return array
     */
    protected function handleRefData($params, $refRes, $annotation, string $field,$source=""): array
    {
        if ($refRes['type'] === "model" && count($refRes['data']) > 0) {
            // 模型引入
            $data = $refRes['data'];
        } else if ($refRes['type'] === "service" && !empty($refRes['data']) && !empty($refRes['data'][$field])) {
            // service引入
            $data = $refRes['data'][$field];
        } else {
            return $params;
        }
        // 过滤field
        if (!empty($annotation->field)) {
            $data = (new Utils())->filterParamsField($data, $annotation->field, 'field');
        }
        // 过滤withoutField
        if (!empty($annotation->withoutField)) {
            $data = (new Utils())->filterParamsField($data, $annotation->withoutField, 'withoutField');
        }

        if (!empty($annotation->value)) {
            $item =  Utils::objectToArray($annotation);
            $item['children'] = $data;
            $item['source'] = $source;
            $param["desc"] = Utils::getLang($item['desc']);

            $children      = $this->handleParamValue($annotation->value, 'param');
            $item['name'] = $children['name'];
            if (count($children['params']) > 0) {
                $item['children'] = Utils::arrayMergeAndUnique("name",$data,$children['params']);
            }
            if ($annotation->type === 'tree' ) {
                // 类型为tree的
                $item['children'][] = [
                    'children' => $item['children'],
                    'name'   =>!empty($annotation->childrenField) ?$annotation->childrenField:'children',
                    'type'   => 'array',
                    'desc'   => Utils::getLang($annotation->childrenDesc),
                ];
            }
            $params[] = $item;


        } else {
            $params = array_merge($params, $data);
        }
        return $params;
    }


    /**
     * 对象分组到tree
     * @param $tree
     * @param $objectData
     * @param string $childrenField
     * @return array
     */
    public function objtctGroupByTree($tree,$objectData,$childrenField='children'){
        $data = [];
        foreach ($tree as $node){
            if (!empty($node[$childrenField])){
                $node[$childrenField] = $this->objtctGroupByTree($node[$childrenField],$objectData);
            }else if (!empty($objectData[$node['name']])){
                $node[$childrenField] =  $objectData[$node['name']];
            }
            $node['menu_key'] = Utils::createRandKey( $node['name']);
            $data[] = $node;
        }
        return $data;
    }

    /**
     * 合并接口到应用分组
     * @param $apiData
     * @param $groups
     * @return array
     */
    public function mergeApiGroup($apiData,$groups){
        if (empty($groups) || count($apiData)<1){
            return $apiData;
        }
        $apiObject = [];
        foreach ($apiData as $controller){
            if (!empty($controller['group'])){
                if (!empty($apiObject[$controller['group']])){
                    $apiObject[$controller['group']][] = $controller;
                }else{
                    $apiObject[$controller['group']] = [$controller];
                }
            }else{
                if (!empty($apiObject['notGroup'])){
                    $apiObject['notGroup'][] = $controller;
                }else{
                    $apiObject['notGroup'] = [$controller];
                }
            }
        }
        if (!empty($apiObject['notGroup'])){
            array_unshift($groups,['title'=>'未分组','name'=>'notGroup']);
        }
        $res = $this->objtctGroupByTree($groups,$apiObject);
        return $res;
    }
}