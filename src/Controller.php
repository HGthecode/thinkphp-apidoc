<?php


namespace hg\apidoc;

use hg\apidoc\Parser;
use think\facade\Request;

class Controller
{
    protected  $config = [
        'title'=>'APi接口文档',
        'copyright'=>'Powered By HG',
        'controllers' => [
        ],
        'versions'=>[
        ],
        'with_cache'=>false,
        'responses'=>'{
            "code":"状态码",
            "message":"操作描述",
            "data":"业务数据",
            "timestamp":"响应时间戳"
        }',
        'global_auth_key'=>"Authorization",
        'auth'=>[
            'with_auth'=>false,
            'auth_password'=>"123456",
            'headers_key'=>"apidocToken",
        ],
        'definitions'=>"hg\apidoc\Definitions",
        'filter_method'=>[
            '_empty'
        ],
    ];

    /**
     * 架构方法 设置参数
     * @param  array $config 配置参数
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig(){
        $config = config('apidoc');
        $this->config = array_merge($this->config, $config);
        if (!empty($this->config['auth'])){
            $this->config['auth'] = [
                'with_auth'=>$this->config['auth']['with_auth'],
                // 验证类型，password=密码验证，只在进入时做密码验证
//                'auth_type'=>$this->config['auth']['auth_type'],
                'headers_key'=>$this->config['auth']['headers_key'],
            ];
        }

        return $this->config;
    }

    /**
     * 验证身份
     */
    public function verifyAuth(){
        $config = config('apidoc');
        $this->config = array_merge($this->config, $config);
        $request = Request::instance();
        $params = $request->param();
        if ($this->config['auth']['with_auth'] === true){
            // 密码验证
            if (md5($this->config['auth']['auth_password']) === $params['password']){
                $token = md5($params['password'].strtotime(date('Y-m-d',time())));
                return array("token"=>$token);
            }else{
                throw new \think\Exception("密码不正确，请重新输入");
            }
        }
        return $params;
    }

    public function verifyToken(){
        $request = Request::instance();
        if (!empty($this->config['auth'])) {
            if ($this->config['auth']['with_auth'] === true){
                $token = $request->header($this->config['auth']['headers_key']);

                if ($token === md5(md5($this->config['auth']['auth_password']).strtotime(date('Y-m-d',time())))){
                    return true;
                }else{
                    throw new \think\exception\HttpException(401, "身份令牌已过期，请重新登录");
                }
            }
        }
        return true;
    }




    /**
     * 获取接口列表
     * @return array
     */
    public function getList()
    {
        $config = config('apidoc');
        $this->config = array_merge($this->config, $config);
        // 验证token身份
        if ($this->config['auth']['with_auth'] === true){
            $tokenRes = $this->verifyToken();
        }

        $request = Request::instance();
        $params = $request->param();
        $version = "";
        if (!empty($params) && !empty($params['version'])){
            $version = $params['version'];
        }
        $cacheFiles= [];
        $cacheName="";
        if ($this->config['with_cache']){
            // 获取缓存数据
            $cachePath = "../runtime/apidoc/".$version;
            if (file_exists($cachePath) && $params['reload']=='false' ){
                $cacheFilePath = "";
                $filePaths=glob($cachePath.'/*.json');
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
                        $list = $fileJson;
                        $cacheName=str_replace(".json","",basename($cacheFilePath));

                    }else{
                        $list = $this->getApiList($version);
                    }
                }else{
                    // 不存在缓存文件，生成数据并存缓存
                    $list = $this->getApiList($version);
                    // 生成缓存数据
                    $cacheName=$this->createJsonFile($list,$version);
                }

            }else{
                // 不存在缓存文件，生成数据并存缓存
                $list = $this->getApiList($version);
                // 生成缓存数据
                $cacheName=$this->createJsonFile($list,$version);
            }
            $filePaths=glob($cachePath.'/*.json');
            if (count($filePaths)>0){
                foreach($filePaths as $item)
                {
                    $cacheFiles[]=str_replace(".json","",basename($item));
                }
            }
        }else{
            $list = $this->getApiList($version);
        }


        $data=array(
            "title"=>$this->config['title'],
            "version"=>$version,
            "copyright"=>$this->config['copyright'],
            "responses"=>$this->config['responses'],
            "list"=>$list,
            "cacheFiles"=>$cacheFiles,
            "cacheName"=>$cacheName
        );

        $res=[
            'code'=>0,
            'data'=>$data,
        ];
        return json($res);

    }

    /**
     * 获取api接口文档
     */
    public function getApiList($version){
        $config = config('apidoc');
        $this->config = array_merge($this->config, $config);
        $list=[];
        $controllers = $this->config['controllers'];
        $versionPath = "";
        if (!empty($version)){
            $versionPath = $version."\\";
        }
        foreach ($controllers as $k => $class) {
            $class = "app\\" .$versionPath. $class;
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $doc_str = $reflection->getDocComment();
                $doc = new Parser($this->config);
                // 解析控制器类的注释
                $class_doc = $doc->parseClass($doc_str);

                // 获取当前控制器Class的所有方法
                $method = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                $filter_method = array_merge(['__construct'], $this->config['filter_method']);
                $actions=[];
                foreach ($method as $j=>$action){
                    // 过滤不解析的方法
                    if(!in_array($action->name, $filter_method))
                    {
                        // 获取当前方法的注释
                        $actionDoc = new Parser($this->config);
                        $actionDocStr = $action->getDocComment();
                        if($actionDocStr)
                        {

                            // 解析当前方法的注释
                            $action_doc = $actionDoc->parseAction($actionDocStr);
//                                $action_doc['name'] = $class."::".$action->name;
                            $action_doc['id'] = $k."-".$j;
//                                // 解析方法
                            $actions[] = $action_doc;
                        }
                    }
                }
                $class_doc['children'] = $actions;
                $class_doc['id'] = $k."";
                if (empty($class_doc['title']) && empty($class_doc['controller'])){
                    $class_doc['title']=$controllers[$k];
                }
                $list[]  = $class_doc;
            }
        }
        return $list;
    }



    /**
     * 获取文件夹内的所有文件
     * @param string $class
     * @param string $action
     *
     * @return array|bool
     */
    protected function listDirFiles($app,$isapp=true)
    {
        $arr = [];
        $base = base_path();
        if($isapp){
            $dir = $base.$app;
        }else{
            $dir = $app;
        }

        if (is_dir($dir)) {//如果是目录，则进行下一步操作
            $d = opendir($dir);//打开目录
            if ($d) {//目录打开正常
                while (($file = readdir($d)) !== false) {//循环读出目录下的文件，直到读不到为止
                    if  ($file != '.' && $file != '..') {//排除一个点和两个点
                        if (is_dir($dir.'/'.$file)) {//如果当前是目录
                            $arr = array_merge($arr,self::listDirFiles($dir.'/'.$file,false));//进一步获取该目录里的文件
                        } else {
                            if(pathinfo($dir.'/'.$file)['extension'] == 'php'){
                                $arr[] = str_replace([$base,'/','.php'],['','\\',''],$dir.'/'.$file);//进一步获取该目录里的文件
                            }
                        }
                    }
                }
            }
            closedir($d);//关闭句柄
        }
        asort($arr);
        return $arr;
    }

    /**
     * 创建接口参数缓存文件
     * @param $json
     * @param $version
     * @return bool|false|string
     */
    protected function createJsonFile($json,$version){
        if (empty($json)){
            return false;
        }
        $fileName =date("Y-m-d H_i_s");
        $fileJson = $json;
        $fileContent = json_encode($fileJson);
        $dir = "../runtime/apidoc/".$version;
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

}