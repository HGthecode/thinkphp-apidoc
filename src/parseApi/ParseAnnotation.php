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
use hg\apidoc\annotation\Author;
use hg\apidoc\annotation\Tag;
use hg\apidoc\annotation\Header;
use hg\apidoc\annotation\Returned;
use hg\apidoc\annotation\ParamType;
use hg\apidoc\annotation\Url;
use hg\apidoc\annotation\Method;
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
        $config      = $this->config;
        $currentApps = (new Utils())->getCurrentApps($appKey);
        $currentApp  = $currentApps[count($currentApps) - 1];

        if (!empty($config['controllers']) && count($config['controllers']) > 0) {
            // 配置的控制器列表
            $controllers = $this->getConfigControllers($currentApp['path']);
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
        $json = array(
            "data"   => $apiData,
            "tags"   => $this->tags,
            "groups" => $this->groups
        );
        return $json;
    }

    /**
     * 获取生成文档的控制器列表
     * @param string $path
     * @return array
     */
    protected function getConfigControllers(string $path): array
    {
        $config      = $this->config;
        $controllers = [];

        $configControllers = $config['controllers'];
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
            if (
                !isset($this->config['filter_controllers']) ||
                (isset($this->config['filter_controllers']) && !in_array($class, $this->config['filter_controllers'])) &&
                $this->config['definitions'] != $class
            ) {
                if (strpos($class, '\\') === false) {
                    $list[] = $appPath . "\\" . $class;
                } else {
                    $list[] = $class;
                }
            }
        }
        return $list;
    }

    protected function parseController($class)
    {
        $config               = $this->config;
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
        $methodList       = [];
        $filter_method    = !empty($config['filter_method']) ? $config['filter_method'] : [];
        $data['menu_key'] = $data['controller'] . "_" . mt_rand(10000, 99999);

        foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
            if (!empty($refMethod->name) && !in_array($refMethod->name, $filter_method)) {
                $methodItem = $this->parseAnnotation($refMethod, true);
                if (!count((array)$methodItem)) {
                    continue;
                }
                $textAnnotations = $this->parseTextAnnotation($refMethod);
                // 标注不解析的方法
                if (in_array("NotParse", $textAnnotations)) {
                    continue;
                }
                // 无标题，且有文本注释
                if (empty($methodItem['title']) && !empty($textAnnotations) && count($textAnnotations) > 0) {
                    $methodItem['title'] = $textAnnotations[0];
                }
                // 添加统一headers请求头参数
                if (!empty($config['headers']) && !in_array("NotHeaders", $textAnnotations)) {
                    if (!empty($methodItem['header'])) {
                        $methodItem['header'] = Utils::arrayMergeAndUnique("name", $config['headers'], $methodItem['header']);
                    } else {
                        $methodItem['header'] = $config['headers'];
                    }
                }
                // 添加统一params请求参数
                if (!empty($config['parameters']) && !in_array("NotParameters", $textAnnotations)) {
                    if (!empty($methodItem['param'])) {
                        $methodItem['param'] = Utils::arrayMergeAndUnique("name", $config['parameters'], $methodItem['param']);
                    } else {
                        $methodItem['param'] = $config['parameters'];
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
                    if (isset($config['responses']['data']) && !$config['responses']['show_responses']) {
                        $responsesData = [];
                    } else if (isset($config['responses']['data']) && $config['responses']['show_responses'] === true) {
                        $responsesData = $config['responses']['data'];
                    } else {
                        $responsesData = $config['responses'];
                    }
                    foreach ($responsesData as $resItem) {
                        if (!empty($resItem['main']) && $resItem['main'] === true) {
                            $resItem['params'] = $methodItem['return'];
                            $hasMian           = true;
                        }
                        $returned[] = $resItem;
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
                // 默认default_author
                if (empty($methodItem['author']) && !empty($config['default_author']) && !in_array("NotDefaultAuthor", $textAnnotations)) {
                    $methodItem['author'] = $config['default_author'];
                }

                // Tags
                if (!empty($methodItem['tag'])) {
                    if (strpos($methodItem['tag'], ' ') !== false) {
                        $tagArr = explode(" ", $methodItem['tag']);
                        foreach ($tagArr as $tag) {
                            if (!in_array($tag, $this->tags)) {
                                $this->tags[] = $tag;
                            }
                        }
                    } else if (!in_array($methodItem['tag'], $this->tags)) {
                        $this->tags[] = $methodItem['tag'];
                    }
                }

                // 无url,自动生成
                if (empty($methodItem['url'])) {
                    $methodItem['url'] = $this->autoCreateUrl($refMethod);
                } else if (!empty($routeGroup->value)) {
                    // 路由分组，url加上分组
                    $methodItem['url'] = '/' . $routeGroup->value . '/' . $methodItem['url'];
                }
                if (!empty($methodItem['url']) && substr($methodItem['url'], 0, 1) != "/") {
                    $methodItem['url'] = "/" . $methodItem['url'];
                }
                $methodItem['name']     = $refMethod->name;
                $methodItem['menu_key'] = $methodItem['method'] . "_" . $refMethod->name . "_" . mt_rand(10000, 99999);

                $methodList[] = $methodItem;

            }

        }
        $data['children'] = $methodList;
        return $data;
    }

    /**
     * 自动生成url
     * @param $method
     * @return string
     */
    protected function autoCreateUrl($method): string
    {
        if (Config::get("controller_auto_search") || !empty($this->config['controller_auto_search'])){
            $pathArr = explode("\\", $method->class );
        }else{
            $searchString = $this->controller_layer . '\\';
            $substr = substr(strstr($method->class, $searchString), strlen($searchString));
            $pathArr = explode("\\", str_replace($substr, str_replace('\\', '.', $substr), $method->class));
        }
        // 控制器地址
        $filterPathNames = array(App::getNamespace(), $this->controller_layer);
        $classPathArr = [];
        foreach ($pathArr as $item) {
            if (!in_array($item, $filterPathNames)) {
                $classPathArr[] = lcfirst($item);
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
                            "desc"    => $item->desc,
                            "default" => $item->default,
                            "require" => $item->require,
                            "childrenType"=> $item->childrenType
                        ];
                        $children      = $this->handleParamValue($item->value);
                        $param['name'] = $children['name'];
                        if (count($children['params']) > 0) {
                            $param['params'] = $children['params'];
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
     * @return array
     */
    protected function parseAnnotation($refMethod, bool $enableRefService = true): array
    {
        $data = [];
        if ($annotations = $this->reader->getMethodAnnotations($refMethod)) {
            $headers = [];
            $params  = [];
            $returns = [];

            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof Param:
                        if (!empty($annotation->ref)) {
                            $refRes = $this->renderRef($annotation->ref, $enableRefService);
                            $params = $this->handleRefData($params, $refRes, $annotation, 'param');
                        } else {
                            $param         = [
                                "name"    => "",
                                "type"    => $annotation->type,
                                "desc"    => $annotation->desc,
                                "default" => $annotation->default,
                                "require" => $annotation->require,
                                "childrenType"=> $annotation->childrenType
                            ];
                            $children      = $this->handleParamValue($annotation->value, 'param');
                            $param['name'] = $children['name'];
                            if (count($children['params']) > 0) {
                                $param['params'] = $children['params'];
                                if ($annotation->type === 'tree' && !empty($annotation->childrenField)) {
                                    // 类型为tree的
                                    $param['params'][] = [
                                        'params' => $children['params'],
                                        'name'   => $annotation->childrenField,
                                        'type'   => 'array',
                                        'desc'   => $annotation->childrenDesc,
                                    ];
                                }
                            }
                            $params[] = $param;

                        }
                        break;
                    case $annotation instanceof Returned:
                        if (!empty($annotation->ref)) {
                            $refRes  = $this->renderRef($annotation->ref, $enableRefService);
                            $returns = $this->handleRefData($returns, $refRes, $annotation, 'return');
                        } else {
                            $param         = [
                                "name"    => "",
                                "type"    => $annotation->type,
                                "desc"    => $annotation->desc,
                                "default" => $annotation->default,
                                "require" => $annotation->require,
                                "childrenType"=> $annotation->childrenType
                            ];
                            $children      = $this->handleParamValue($annotation->value, 'return');
                            $param['name'] = $children['name'];
                            if (count($children['params']) > 0) {
                                $param['params'] = $children['params'];
                                if ($annotation->type === 'tree' && !empty($annotation->childrenField)) {
                                    // 类型为tree的
                                    $param['params'][] = [
                                        'params' => $children['params'],
                                        'name'   => $annotation->childrenField,
                                        'type'   => 'array',
                                        'desc'   => $annotation->childrenDesc,
                                    ];
                                }
                            }
                            $returns[] = $param;
                        }
                        break;
                    case $annotation instanceof Header:
                        if (!empty($annotation->ref)) {
                            $refRes  = $this->renderRef($annotation->ref, $enableRefService);
                            $headers = $this->handleRefData($headers, $refRes, $annotation, 'header');
                        } else {
                            $param     = [
                                "name"    => $annotation->value,
                                "desc"    => $annotation->desc,
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
                        $data['title'] = $annotation->value;
                        break;
                    case $annotation instanceof Desc:
                        $data['desc'] = $annotation->value;
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
                }
            }
            if ($headers && count($headers) > 0) {
                $data['header'] = $headers;
            }
            $data['param']  = $params;
            $data['return'] = $returns;
        }
        return $data;
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
     * @return array
     */
    protected function handleRefData($params, $refRes, $annotation, string $field): array
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
            $item = [
                'name'    => $annotation->value,
                'desc'    => $annotation->desc,
                'type'    => $annotation->type,
                'require' => $annotation->require,
                'default' => $annotation->default,
                'params'  => $data
            ];
            if ($annotation->type === 'tree') {
                // 类型为tree的
                $item['params'][] = [
                    'params' => $data,
                    'name'   => !empty($annotation->childrenField)?$annotation->childrenField:"children",
                    'type'   => 'array',
                    'desc'   => $annotation->childrenDesc,
                ];
                $params[]         = $item;
            } else {
                $params[] = $item;
            }
        } else {
            $params = array_merge($params, $data);
        }
        return $params;
    }
}