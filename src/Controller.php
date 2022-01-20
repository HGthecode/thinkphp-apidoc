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
use think\facade\Lang;
use think\facade\Request;

class Controller
{
    protected $app;

    protected $config;

    /**
     * @var int tp版本
     */
    protected $tp_version;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->tp_version = substr(\think\facade\App::version(), 0, 2) == '5.'? 5: 6;
        $config = Config::get("apidoc")?Config::get("apidoc"):Config::get("apidoc.");
        if (!(!empty($config['apps']) && count($config['apps']))){
            $default_app = Config::get("app.default_app")?Config::get("app.default_app"):Config::get("app.default_module");
            $namespace = \think\facade\App::getNamespace();
            // tp5获取 application
            if ($this->tp_version === 5){
                $appPath = \think\facade\App::getAppPath();
                $appPathArr = explode("\\", $appPath);
                for ($i = count($appPathArr)-1; $i>0 ;$i--){
                    if ($appPathArr[$i]){
                        $namespace = $appPathArr[$i];
                        break;
                    }
                }
            }
            $path = $namespace.'\\'.$default_app.'\\controller';
            if (!is_dir($path)){
                $path =$namespace.'\\controller';
            }
            $defaultAppConfig = ['title'=>$default_app,'path'=>$path,'folder'=>$default_app];
            $config['apps'] = [$defaultAppConfig];
        }
        // 过滤关闭的生成器
        if (!empty($config['generator']) && count($config['generator'])){
            $generatorList =[];
            foreach ($config['generator'] as $item) {
                if (!isset($item['enable']) || (isset($item['enable']) && $item['enable']===true)){
                    $generatorList[]=$item;
                }
            }
            $config['generator'] = $generatorList;
        }


        Config::set(['apidoc'=>$config]);
        $this->config = $config;



    }

    /**
     * 获取配置
     * @return \think\response\Json
     */
    public function getConfig(){
        $config = $this->config;
        if (!empty($config['auth'])){
            unset($config['auth']['password']);
            unset($config['auth']['key']);
        }
        $request = Request::instance();
        $params = $request->param();

        if (!empty($params['lang'])){
            if ($this->tp_version === 5){
                Lang::setLangCookieVar($params['lang']);
            }else{
                Lang::setLangSet($params['lang']);
                \think\facade\App::loadLangPack($params['lang']);
            }

        }
        $config['title'] = Utils::getLang($config['title']);
        $config['desc'] = Utils::getLang($config['desc']);
        $config['headers'] = Utils::getArrayLang($config['headers'],"desc");
        $config['parameters'] = Utils::getArrayLang($config['parameters'],"desc");
        $config['responses'] = Utils::getArrayLang($config['responses'],"desc");


        // 清除apps配置中的password
        $config['apps'] = (new Utils())->handleAppsConfig($config['apps'],true);
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
            $res = [
                "token"=>$hasAuth
            ];
            return Utils::showJson(0,"",$res);
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
        $lang = "";

        if (!empty($params['lang'])){
            $lang = $params['lang'];
            if ($this->tp_version === 5){
                Lang::setLangCookieVar($lang);
            }else{
                \think\facade\App::loadLangPack($lang);
            }

        }

        if (!empty($params['appKey'])){
            // 获取指定应用
            $appKey = $params['appKey'];
        }else{
            // 获取默认控制器
            $default_app = $config = Config::get("app.default_app");
            $appKey = $default_app;
        }
        $currentApps = (new Utils())->getCurrentApps($appKey);
        $currentApp  = $currentApps[count($currentApps) - 1];

        (new Auth())->checkAuth($appKey);

        $cacheData=null;
        if (!empty($config['cache']) && $config['cache']['enable']){
            $cacheKey = $appKey."_".$lang;
            $cacheData = (new CacheApiData())->get($cacheKey);
            if ($cacheData && empty($params['reload'])){
                $apiData = $cacheData;
            }else{
                // 生成数据并缓存
                $apiData = (new ParseAnnotation())->renderApiData($appKey);
                (new CacheApiData())->set($cacheKey,$apiData);
            }
        }else{
            // 生成数据
            $apiData = (new ParseAnnotation())->renderApiData($appKey);
        }

        // 接口分组
        if (!empty($currentApp['groups'])){
            $data = (new ParseAnnotation())->mergeApiGroup($apiData['data'],$currentApp['groups']);
        }else{
            $data = $apiData['data'];
        }
        $groups=!empty($currentApp['groups'])?$currentApp['groups']:[];
        $json=[
            'data'=>$data,
            'app'=>$currentApp,
            'groups'=>$groups,
            'tags'=>$apiData['tags'],
        ];

        return Utils::showJson(0,"",$json);
    }

    public function getMdMenus(){
        // 获取md
        $request = Request::instance();
        $params = $request->param();
        $lang = "";
        if (!empty($params['lang'])){
            $lang = $params['lang'];
            if ($this->tp_version === 5){
                Lang::setLangCookieVar($params['lang']);
            }else{
                Lang::setLangSet($params['lang']);
                \think\facade\App::loadLangPack($params['lang']);
            }
        }
        if (!empty($params['appKey'])){
            // 获取指定应用
            $appKey = $params['appKey'];
        }else{
            // 获取默认控制器
            $default_app = $config = Config::get("app.default_app");
            $appKey = $default_app;
        }
        (new Auth())->checkAuth($appKey);

        $docs = (new ParseMarkdown())->getDocsMenu($lang);
        return Utils::showJson(0,"",$docs);

    }

    /**
     * 获取md文档内容
     * @return \think\response\Json
     */
    public function getMdDetail(){
        $request = Request::instance();
        $params = $request->param();
        if (!empty($params['lang'])){
            if ($this->tp_version === 5){
                Lang::setLangCookieVar($params['lang']);
            }else{
                Lang::setLangSet($params['lang']);
                \think\facade\App::loadLangPack($params['lang']);
            }
        }
        try {
            if (empty($params['path'])){
                throw new ErrorException("mdPath not found");
            }
            if (empty($params['appKey'])){
                throw new ErrorException("appkey not found");
            }
            $lang="";
            if (!empty($params['lang'])){
                $lang=$params['lang'];
            }
            (new Auth())->checkAuth($params['appKey']);
            $content = (new ParseMarkdown())->getContent($params['appKey'],$params['path'],$lang);
            $res = [
                'content'=>$content,
            ];
            return Utils::showJson(0,"",$res);

        } catch (ErrorException $e) {
            return Utils::showJson($e->getCode(),$e->getMessage());
        }
    }


    public function createGenerator(){
        $request = Request::instance();
        $params = $request->param();
        $res = (new generator\Index())->create($params);
        return Utils::showJson(0,"",$res);
    }








}