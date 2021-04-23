<?php
declare(strict_types = 1);

namespace hg\apidoc;

use hg\apidoc\exception\AuthException;
use hg\apidoc\exception\ErrorException;
use hg\apidoc\parseApi\CacheApiData;
use hg\apidoc\parseApi\ParseAnnotation;
use hg\apidoc\parseApi\ParseMarkdown;
use think\App;
use think\facade\Config;
use think\facade\Request;
use hg\apidoc\crud\CreateCrud;

class Controller
{
    protected $app;

    protected $config;

    protected $defaultConfig=[
        'crud'=>[
            'model'=>[
                'fields_types'=>[
                    "int",
                    "tinyint",
                    "smallint",
                    "mediumint",
                    "integer",
                    "bigint",
                    "bit",
                    "real",
                    "float",
                    "decimal",
                    "numeric",
                    "char",
                    "varchar",
                    "date",
                    "time",
                    "year",
                    "timestamp",
                    "datetime",
                    "tinyblob",
                    "blob",
                    "mediumblob",
                    "longblob",
                    "tinytext",
                    "text",
                    "mediumtext",
                    "longtext",
                    "enum",
                    "set",
                    "binary",
                    "varbinary",
                    "point",
                    "linestring",
                    "polygon",
                    "geometry",
                    "multipoint",
                    "multilinestring",
                    "multipolygon",
                    "geometrycollection"
                ]
            ]
        ]
    ];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = Config::get("apidoc");
    }

    /**
     * 获取配置
     * @return \think\response\Json
     */
    public function getConfig(){
        $config = $this->config;
        if (!empty($config['auth'])){
            unset($config['auth']['auth_password']);
            unset($config['auth']['password']);
            unset($config['auth']['key']);
        }
        // 处理统一返回信息
        if (!empty($config['responses']) && is_string($config['responses'])){
            // 兼容原配置
            $config['responses'] = [
                'jsonStr'=>$config['responses']
            ];
        }else if (!empty($config['responses']) && isset($config['responses']['show_responses']) && !$config['responses']['show_responses'] && !empty($config['responses']['data'])){
            // 显示在提示中
            $responsesStr = '{'."\r\n";
            $responsesMain = "";
            foreach ($config['responses']['data'] as $item){
                $responsesStr.='"'.$item['name'].'":"'.$item['desc'].'",'."\r\n";
                if (!empty($item['main']) && $item['main']==true){
                    $responsesMain = $item;
                }
            }
            $responsesStr.= '}';
            $config['responses']['jsonStr']=$responsesStr;
            $config['responses']['main']=$responsesMain;
        }

        $config['debug']=app()->isDebug();

        if (!empty($config['crud'])){
            // 无配置可选字段类型，使用默认的
            if (!empty($config['crud']['model']) && empty($config['crud']['model']['fields_types'])){
                $config['crud']['model']['fields_types'] = $this->defaultConfig['crud']['model']['fields_types'];
            }
            // 过滤route文件配置
            if (!empty($config['crud']['route'])){
                unset($config['crud']['route']);
            }
        }
        // 清除apps配置中的password
        $config['apps'] = (new Utils())->handleAppsConfig($config['apps']);
        return Utils::showJson(0,"",$config);
    }

    /**
     * 验证密码
     * @return false|\think\response\Json
     * @throws \think\Exception
     */
    public function verifyAuth(){
        $config = $this->config;

        $request = Request::instance();
        $params = $request->param();
        $password = $params['password'];
        if (empty($password)){
            throw new AuthException( "password not found");
        }
        $appKey = !empty($params['appKey'])?$params['appKey']:"";

        if (!$appKey && !(!empty($config['auth']) && $config['auth']['enable'])) {
            return false;
        }
        try {
            $hasAuth = (new Auth())->verifyAuth($password,$appKey);
            return Utils::showJson(0,"",$hasAuth);
        } catch (AuthException $e) {
            return Utils::showJson($e->getCode(),$e->getMessage());
        }

    }

    /**
     * 获取文档数据
     * @return \think\response\Json
     */
    public function getApidoc(){

        $config = $this->config;
        $request = Request::instance();
        $params = $request->param();
        (new Auth())->checkAuth($params['appKey']);

        $cacheData=null;
        if (!empty($config['cache']) && $config['cache']['enable']){
            $cacheData = (new CacheApiData())->get($params['appKey'],$params['cacheFileName']);
            if ($cacheData && $params['reload']=='false'){
                $apiData = $cacheData['data'];
            }else{
                // 生成数据并缓存
                $apiData = (new ParseAnnotation())->renderApiData($params['appKey']);
                $cacheData =(new CacheApiData())->set($params['appKey'],$apiData);
            }
        }else{
            // 生成数据
            $apiData = (new ParseAnnotation())->renderApiData($params['appKey']);
        }
        $groups=[['title'=>'全部','name'=>0]];
        // 获取md
        $docs = (new ParseMarkdown())->getDocsMenu();
        if (count($docs)>0){
            $menu_title = !empty($config['docs']) && !empty($config['docs']['menu_title'])?$config['docs']['menu_title']:'文档';
            $groups[]=['title'=>$menu_title,'name'=>'markdown_doc'];
        }
        if (!empty($config['groups']) && count($config['groups'])>0 && !empty($apiData['groups']) && count($apiData['groups'])>0){
            $configGroups=[];
            foreach ($config['groups'] as $group) {
                if (in_array($group['name'],$apiData['groups'])){
                    $configGroups[]=$group;
                }
            }
            $groups = array_merge($groups,$configGroups);
        }
        $json=[
            'groups'=>$groups,
            'list'=>$apiData['data'],
            'docs'=>$docs,
            'tags'=>$apiData['tags']
        ];
        if ($cacheData && !empty($cacheData['list'])){
            $json['cacheFiles']=$cacheData['list'];
            $json['cacheName']=$cacheData['name'];
        }
        return Utils::showJson(0,"",$json);
    }

    /**
     * 获取md文档内容
     * @return \think\response\Json
     */
    public function getMdDetail(){
        $request = Request::instance();
        $params = $request->param();
        try {
            if (empty($params['path'])){
                throw new ErrorException("mdPath not found");
            }
            if (empty($params['appKey'])){
                throw new ErrorException("appkey not found");
            }
            (new Auth())->checkAuth($params['appKey']);
            $res = (new ParseMarkdown())->getContent($params['appKey'],$params['path']);
            return Utils::showJson(0,"",$res);

        } catch (ErrorException $e) {
            return Utils::showJson($e->getCode(),$e->getMessage());
        }
    }


    /**
     * 创建Crud
     * @return \think\response\Json
     */
    public function createCrud()
    {
        if (!\think\facade\App::isDebug()) {
            throw new ErrorException("no debug",501);
        }
        $request = Request::instance();
        $params  = $request->param();

        if (!isset($params['appKey'])){
            throw new ErrorException("appkey not found");
        }
        (new Auth())->checkAuth($params['appKey']);
        $res = (new CreateCrud())->create($params);
        return Utils::showJson(0,"",$res);



    }







}