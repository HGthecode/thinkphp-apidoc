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

    
    public function __construct(App $app,Reader $reader)
    {
        $this->app = $app;
        $this->reader = $reader;
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig(){
        $config = $this->app->config->get('apidoc');
        if (!empty($config['auth'])) {
            unset($config['auth']['auth_password']);
            unset($config['auth']['password']);
            unset($config['auth']['key']);
        }
        return $this->showJson(0,"",$config);
    }



    /**
     * 验证Token
     */
    public function checkAuth(){
        $config = $this->app->config->get('apidoc');
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
        $config = $this->app->config->get('apidoc');
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
     * 获取文档数据
     * @return \think\response\Json
     */
    public function getData(){
        $config = $this->app->config->get('apidoc');
        // 验证token身份
        if ($config['auth']['enable']){
            $tokenRes = $this->checkAuth();
            if (!$tokenRes){
                throw new \think\exception\HttpException(401, "token失效");
            }
        }

        $request = Request::instance();
        $params = $request->param();
        $version = "";
        if (!empty($params) && !empty($params['version'])){
            $version = $params['version'];
        }
        if ($config['cache']['enable']){
            // 获取缓存数据
            $path = !empty($config['cache']) && !empty($config['cache']['path'])?$config['cache']['path']:'../runtime/apidoc/';
            $cachePath = $path.$version;
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
                        $json = $this->reloadData($version,$params);
                    }
                }else{
                    // 不存在缓存文件，生成数据并存缓存
                    $json = $this->reloadData($version,$params);
                    $cacheName=$this->createCacheFile($json,$version);
                    if ($config['cache']['max'] && count($filePaths)>=$config['cache']['max']){
                        //达到最大数量，删除第一个
                        $this->delCacheFile($filePaths[0]);
                    }
                }

            }else{
                // 不存在缓存文件，生成数据并存缓存
                $json = $this->reloadData($version,$params);
                $cacheName=$this->createCacheFile($json,$version);
                if ($config['cache']['max'] && count($filePaths)>=$config['cache']['max']){
                    //达到最大数量，删除第一个
                    $this->delCacheFile($filePaths[0]);
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
            $json = $this->reloadData($version,$params);
        }

        return $this->showJson(0,"",$json);




    }


    /**
     * 生成文档数据
     * @param $version
     * @param $params
     * @return array
     */
    public function reloadData($version,$params)
    {
        $config = $this->app->config->get('apidoc');
        $apiData = $this->renderApiData($version);

        $groups=[];
        // 获取md
        $docs=[];
        if (!empty($config['docs']) && !empty($config['docs']['menus']) && count($config['docs']['menus'])>0){
            $docs = $this->renderDocs($config['docs']['menus'],$version);
            $menu_title = !empty($config['docs']) && !empty($config['docs']['menu_title'])?$config['docs']['menu_title']:'文档';
            $groups[]=['title'=>$menu_title,'name'=>'markdown_doc'];
        }
        if (!empty($config['groups']) && count($config['groups'])>0){
            array_unshift($groups, ['title'=>'全部','name'=>0]);
            $groups = array_merge($groups,$config['groups']);
        }

        $json=[
            'groups'=>$groups,
            'list'=>$apiData,
            'docs'=>$docs
        ];

        return $json;

    }

    /**
     * 获取生成文档的控制器
     * @param $versionPath
     * @return array
     */
    protected function getConfigControllers($versionPath){
        $config = $this->app->config->get('apidoc');
        $controllers=[];

        $configControllers = $config['controllers'];
        if (!empty($configControllers) && count($configControllers)>0){
            foreach ($configControllers as $item){
                $class = $versionPath. $item;
                if (class_exists($class)) {
                    $controllers[]=$class;
                }
            }
        }
        return $controllers;
    }

    /**
     * 获取目录下的控制器
     * @param $versionPath
     * @return array
     */
    protected function getDirControllers($versionPath){
        $dir = $this->app->getAppPath() . $this->app->config->get('route.controller_layer');
        $controllers=[];
        if (is_dir($dir)) {
            $controllers =$this->scanDir($dir,$versionPath);
        }
        return $controllers;
    }

    /**
     * 处理目录下的控制器
     * @param $dir
     * @param $versionPath
     * @return array
     */
    protected function scanDir($dir,$versionPath)
    {
        $list=[];
        $config = $this->app->config->get('apidoc');
        foreach (ClassMapGenerator::createMap($dir) as $class => $path) {
            if (
                (
                    empty($versionPath) ||
                    (!empty($versionPath) && strpos($class,$versionPath) !== false)
                ) &&
                !(in_array($class,$config['filter_controllers']))
            ) {
                $list[] = $class;
            }
        }
        return $list;
    }

    protected function getPathByVersion($version){
        $config = $this->app->config->get('apidoc');
        $versionPath = "";
        if (!empty($version)){
            foreach ($config['versions'] as $item){
                if ($item['title'] == $version){
                    $versionPath = $item['folder']."\\";
                    break;
                }
            }
        }
        return $versionPath;
    }

    /**
     * 生成api接口数据
     * @param $version
     * @return array
     */
    public function renderApiData($version){
        $config = $this->app->config->get('apidoc');
        $apiData=[];
        $versionPath = $this->getPathByVersion($version);
        if (!empty($config['controllers']) && count($config['controllers'])>0){
            // 配置的控制器列表
            $controllers = $this->getConfigControllers($versionPath);
        }else{
            // 默认读取所有的
            $controllers = $this->getDirControllers($versionPath);
        }

        foreach ($controllers as $class){
            $apiData[]=$this->parseController($class);
        }

        return $apiData;
    }

    /**
     * 创建接口参数缓存文件
     * @param $json
     * @param $version
     * @return bool|false|string
     */
    protected function createCacheFile($json,$version){
        if (empty($json)){
            return false;
        }
        $config = $this->app->config->get('apidoc');
        $path = !empty($config['cache']) && !empty($config['cache']['path'])?$config['cache']['path']:'../runtime/apidoc/';
        $fileName =date("Y-m-d H_i_s");
        $fileJson = $json;
        $fileContent = json_encode($fileJson);
        $dir = $path.$version;
        $path = $dir."/".$fileName.".json";
        //判断文件夹是否存在
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $myfile = fopen($path, "w") or die("Unable to open file!");
        fwrite($myfile, $fileContent);
        fclose($myfile);
        return $fileName;
    }

    /**
     * 删除缓存文件
     * @param $path
     */
    protected function delCacheFile($path){
        $url=iconv('utf-8','gbk',$path);
        if(PATH_SEPARATOR == ':'){ //linux
            unlink($path);
        }else{  //Windows
            unlink($url);
        }
    }


}