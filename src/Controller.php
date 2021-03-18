<?php


namespace hg\apidoc;

use think\facade\Request;
use think\App;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use think\facade\Cache;



class Controller
{
    use ParseAnnotation,ParseMarkdown;

    /**
     * @var App 
     */
    protected $app;

    /**
     * @var Reader
     */
    protected $reader;

    // 当前应用的所有节点
    protected $currentApps;
    // 当前应用
    protected $currentApp;

    //tags，当前应用/版本所有的tag
    protected $tags=array();

    //groups,当前应用/版本的分组name
    protected $groups=array();

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

    protected $config;

    
    public function __construct(App $app,Reader $reader)
    {
        $this->app = $app;
        $this->reader = $reader;
        $this->config = $app->config->get('apidoc');
    }

    /**
     * 获取配置
     * @return array
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
        $config['debug']=$this->app->isDebug();


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

        return $this->showJson(0,"",$config);
    }



    /**
     * 验证Token
     */
    public function checkAuth(){
        $config = $this->config;
        if (!(!empty($config['auth']) && $config['auth']['enable'])) {
            return true;
        }
        $request = Request::instance();
        $headers_key = !empty($config['auth']) && !empty($config['auth']['headers_key'])?$config['auth']['headers_key']:"apidocToken";
        $token = $request->header($headers_key);
        if (empty($token)){
            throw new \think\exception\HttpException(401, "无token");
        }
        $hasAuth = (new Auth($config))->checkToken($token);
        return $hasAuth;
    }

    /**
     * 登录，密码验证
     */
    public function verifyAuth(){
        $config = $this->config;
        if (!(!empty($config['auth']) && $config['auth']['enable'])) {
            return false;
        }
        $request = Request::instance();
        $params = $request->param();
        $password = $params['password'];
        if (empty($password)){
            throw new \think\exception\HttpException(415, "请输入密码");
        }
        $hasAuth = (new Auth($config))->verifyAuth($password);
        return $this->showJson(0,"",$hasAuth);
    }

    /**
     * 统一返回json
     * @param int $code
     * @param string $msg
     * @param string $data
     * @return \think\response\Json
     */
    private function showJson($code=0,$msg="",$data=""){
        $res=[
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data,
        ];
        return json($res);
    }

    /**
     * 创建Crud
     * @return \think\response\Json
     */
    public function createCrud(){
        if (!$this->app->isDebug()){
            throw new \think\exception\HttpException(415, "请在debug模式下，使用该功能");
        }

        $config = $this->config;
        $request = Request::instance();
        $params = $request->param();
        $this->initCurrentApps($params['appKey']);
        $res = (new CreateCrud($config,$this->currentApps,$this->app))->create($params);
        return $this->showJson(0,"",$res);
    }


    /**
     * 初始化当前所选的应用/版本数据
     * @param $appKey
     */
    protected function initCurrentApps($appKey){
        $config = $this->config;
        if (strpos($appKey,'_')!==false){
            $keyArr = explode("_", $appKey);
        }else{
            $keyArr =[$appKey];
        }
        $this->currentApps = (new Utils())->getTreeNodesByKeys($config['apps'],$keyArr,'folder','items');
        $this->currentApp = $this->currentApps[count($this->currentApps)-1];
    }

    /**
     * 获取文档数据
     * @return \think\response\Json
     */
    public function getData(){
        $config = $this->config;
        // 验证token身份
        if ($config['auth']['enable']){
            $tokenRes = $this->checkAuth();
            if (!$tokenRes){
                throw new \think\exception\HttpException(401, "token失效");
            }
        }

        $request = Request::instance();
        $params = $request->param();
        if (!empty($params) && !empty($params['appKey'])){
           $this->initCurrentApps($params['appKey']);
        }
        if ($config['cache']['enable']){
            // 获取缓存数据
            $path = !empty($config['cache']) && !empty($config['cache']['path'])?$config['cache']['path']:'../runtime/apidoc/';
            $cacheAppPath="";
            if (!empty($this->currentApps) && count($this->currentApps)>0){
                foreach ($this->currentApps as $keyIndex=>$appItem){
                    $cacheAppPath.=$appItem['folder']."/";
                }
            }
            $cachePath = $path.$cacheAppPath;
            $cacheFiles= [];
            $cacheName="";
            $filePaths=glob($cachePath.'/*.json');
            if (file_exists($cachePath) && $params['reload']=='false' ){
                $cacheFilePath = "";
                if (count($filePaths)>0){
                    $cacheFilePath =  $filePaths[count($filePaths)-1];
                }
                if (!empty($params) && !empty($params['cacheFileName'])){
                    // 前端传入的缓存文件名
                    $cacheFileName = $params['cacheFileName'];
                    $cacheFilePath = $cachePath."/".$cacheFileName.'.json';
                }
                if ($cacheFilePath && file_exists($cacheFilePath)){
                    $fileContent= file_get_contents($cacheFilePath);
                    if (!empty($fileContent)){
                        $fileJson = json_decode($fileContent);
                        $json = $fileJson;
                        $cacheName=str_replace(".json","",basename($cacheFilePath));
                    }else{
                        $json = $this->reloadData($params);
                    }
                }else{
                    // 不存在缓存文件，生成数据并存缓存
                    $json = $this->reloadData($params);
                    $cacheName=$this->createCacheFile($json);
                    if ($config['cache']['max'] && count($filePaths)>=$config['cache']['max']){
                        //达到最大数量，删除第一个
                        Utils::delFile($filePaths[0]);
                    }
                }

            }else{
                // 不存在缓存文件，生成数据并存缓存
                $json = $this->reloadData($params);
                $cacheName=$this->createCacheFile($json);
                if ($config['cache']['max'] && count($filePaths)>=$config['cache']['max']){
                    //达到最大数量，删除第一个
                    Utils::delFile($filePaths[0]);
                }
            }
            $filePaths=glob($cachePath.'/*.json');
            if (count($filePaths)>0){
                foreach($filePaths as $item)
                {
                    $cacheFiles[]=str_replace(".json","",basename($item));
                }
            }
            if (is_array($json)){
                $json['cacheFiles']=$cacheFiles;
                $json['cacheName']=$cacheName;
            }else{
                $json->cacheFiles=$cacheFiles;
                $json->cacheName=$cacheName;
            }


        }else{
            $json = $this->reloadData($params);
        }

        return $this->showJson(0,"",$json);
    }


    /**
     * 生成文档数据
     * @param $params
     * @return array
     */
    public function reloadData($params)
    {
        $config = $this->config;
        $apiData = $this->renderApiData();

        $groups=[['title'=>'全部','name'=>0]];
        // 获取md
        $docs=[];
        if (!empty($config['docs']) && !empty($config['docs']['menus']) && count($config['docs']['menus'])>0){
            $docs = $this->renderDocs($config['docs']['menus']);
            $menu_title = !empty($config['docs']) && !empty($config['docs']['menu_title'])?$config['docs']['menu_title']:'文档';
            $groups[]=['title'=>$menu_title,'name'=>'markdown_doc'];
        }
        if (!empty($config['groups']) && count($config['groups'])>0 && !empty($this->groups) && count($this->groups)>0){
            $configGroups=[];
            foreach ($config['groups'] as $group) {
                if (in_array($group['name'],$this->groups)){
                    $configGroups[]=$group;
                }
            }
            $groups = array_merge($groups,$configGroups);
        }

        $json=[
            'groups'=>$groups,
            'list'=>$apiData,
            'docs'=>$docs,
            'tags'=>$this->tags
        ];
        return $json;
    }

    /**
     * 获取生成文档的控制器
     * @param $path
     * @return array
     */
    protected function getConfigControllers($path){
        $config = $this->config;
        $controllers=[];

        $configControllers = $config['controllers'];
        if (!empty($configControllers) && count($configControllers)>0){
            foreach ($configControllers as $item){
                $itemPath = $item;
                $class = $path.'\\'. $itemPath;
                if (class_exists($class)) {
                    $controllers[]=$class;
                }
            }
        }
        return $controllers;
    }

    /**
     * 获取目录下的控制器
     * @param $path
     * @return array
     */
    protected function getDirControllers($path){
        if ($path){
            $pathStr = str_replace("\\","/",$path);
            $dir = $this->app->getRootPath() . $pathStr;
        }else{
            $dir = $this->app->getRootPath() . $this->app->config->get('route.controller_layer');
        }
        $controllers=[];
        if (is_dir($dir)) {
            $controllers =$this->scanDir($dir,$path);
        }
        return $controllers;
    }

    /**
     * 处理目录下的控制器
     * @param $dir
     * @param $appPath
     * @return array
     */
    protected function scanDir($dir,$appPath)
    {
        $list=[];
        foreach (ClassMapGenerator::createMap($dir) as $class => $path) {
            if (
                !isset($this->config['filter_controllers']) ||
                (isset($this->config['filter_controllers']) &&  !in_array($class,$this->config['filter_controllers'])) &&
                $this->config['definitions'] != $class
            ){
                $list[] = $class;
            }
        }
        return $list;
    }

    /**
     * 生成api接口数据
     * @return array
     */
    public function renderApiData(){
        $config = $this->config;
        $apiData=[];
        $path = $this->currentApp['path'];
        if (!empty($config['controllers']) && count($config['controllers'])>0){
            // 配置的控制器列表
            $controllers = $this->getConfigControllers($path);
        }else{
            // 默认读取所有的
            $controllers = $this->getDirControllers($path);
        }

        foreach ($controllers as $class){
            $classData=$this->parseController($class);
            if ($classData!==false){
                $apiData[] = $classData;
            }
        }
        return $apiData;
    }

    /**
     * 创建接口参数缓存文件
     * @param $json
     * @return bool|false|string
     */
    protected function createCacheFile($json){
        if (empty($json)){
            return false;
        }
        $config = $this->config;
        $path = !empty($config['cache']) && !empty($config['cache']['path'])?$config['cache']['path']:'../runtime/apidoc/';
        $fileName =date("Y-m-d H_i_s");
        $fileJson = $json;
        $fileContent = json_encode($fileJson);
        $dir = $path;
        if (!empty($this->currentApps) && count($this->currentApps)>0){
            foreach ($this->currentApps as $appItem){
                $dir.="/".$appItem['folder'];
            }
        }
        $path = $dir."/".$fileName.".json";
        Utils::createFile($path,$fileContent);
        return $fileName;
    }


}