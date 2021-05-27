<?php


namespace hg\apidoc\crud;


use hg\apidoc\exception\ErrorException;
use think\Db as Db5;
use think\facade\Config;
use think\facade\Db;
use think\facade\App;
use hg\apidoc\Utils;


class CreateCrud
{
    protected $config;

    protected $currentApps;

    protected $controller_layer = "";

    public function __construct()
    {
        $config = Config::get('apidoc')?Config::get('apidoc'):Config::get('apidoc.');
        $this->controller_layer = Config::get('route.controller_layer',"controller");
        if (!empty($config) && !empty($config['crud'])){
            $this->config = $config['crud'];
        }else{
            throw new ErrorException("no config crud",501);
        }

    }

    /**
     * 创建Crud
     * @param $params
     * @return array
     */
    public function create($params){

        $appKey = $params['appKey'];
        $currentApps = (new Utils())->getCurrentApps($appKey);
//        $currentApp  = $currentApps[count($currentApps) - 1];

        $data = $this->renderTemplateData($params,$currentApps);
        $res = [];
        // 创建数据表
        if (!empty($params['model']['table'])){
            $sqlRes = $this->createModelTable($params['model'],$params['title']);
            if ($sqlRes == true){
                $res[]="创建数据表成功";
            }else{
//                $msg= $sqlRes?$sqlRes:"数据表创建失败，请检查配置";
                throw new ErrorException("datatable crud error",500);
            }
        }
        // 生成文件
        $keys = array_keys($this->config);
        foreach ($keys as $index=>$key) {
            // 添加路由
            if (
                $key==="route" &&
                !empty($this->config['route']) &&
                !empty($this->config['route']['template']) &&
                !empty($this->config['route']['path'])
            ){
                $tmp_path = (new Utils())->replaceCurrentAppTemplate($this->config['route']['template'],$currentApps);
                $tempPath = $tmp_path.".txt";
                $str_tmp = Utils::getFileContent($tempPath);
                if (!empty($str_tmp)){
                    $tmp_content = Utils::replaceTemplate($str_tmp,$data);
                    $tmp_content = (new Utils())->replaceCurrentAppTemplate($tmp_content,$currentApps);
                    $routePathStr = (new Utils())->replaceCurrentAppTemplate($this->config['route']['path'],$currentApps);
                    $routePathStr = str_replace("\\","/",$routePathStr);
                    $routePath = App::getAppPath().$routePathStr;
                    $routeContent = Utils::getFileContent($routePath);
                    $routeContent.="\r\n".$tmp_content."\r\n";
                    Utils::createFile($routePath,$routeContent);
                    $res[]="添加路由成功";
                }

            }else{
                $currentParam = $params[$key];
                $tmp_path = (new Utils())->replaceCurrentAppTemplate($currentParam['template'],$currentApps);
                $tempPath = $tmp_path.".txt";
                $str_tmp = Utils::getFileContent($tempPath);
                $file_content = Utils::replaceTemplate($str_tmp,$data);
                $file_content = (new Utils())->replaceCurrentAppTemplate($file_content,$currentApps);
                $namespacePath = str_replace("\\","/",$currentParam['path']);
                $filePath = '../'.$namespacePath.'/'.$currentParam['class_name'].'.php';
                $fp=Utils::createFile($filePath,$file_content);
                if ($fp){
                    $res[]="创建文件成功 path:".$filePath;
                }
            }
        }

        return $res;
    }


    /**
     * 生成模板变量的数据
     * @param $params
     * @return array
     */
    public function renderTemplateData($params,array $currentApps){
        // 模板参数
        $api_group = "";
        if (!empty($params['group'])){
            $api_group = '@Apidoc\Group("'.$params['group'].'")';
        }
        $data=[
            'title'=>!empty($params['title'])?$params['title']:"",
            'api_group'=>$api_group,
        ];
        $keys = array_keys($this->config);
        foreach ($keys as $index=>$key){
            $currentConfig = $this->config[$key];
            //验证模板是否存在
            $tmp_path = (new Utils())->replaceCurrentAppTemplate($currentConfig['template'],$currentApps);
            if(!file_exists($tmp_path.'.txt')){
                throw new ErrorException("template not found",404,[
                    'template'=>$currentConfig['template']
                ]);
            }
            if ($key==="route"){
                continue;
            }
            $currentParam = $params[$key];
            if(!preg_match("/^[A-Z]{1}[A-Za-z0-9]{1,32}$/",$currentParam['class_name'])){
                throw new ErrorException("classname error",412,[
                    'classname'=>$currentParam['class_name']
                ]);
            }
            $currentParamPath = str_replace("\\","/",$currentParam['path']);
            // 验证目录是否存在
            if(!is_dir('../'.$currentParamPath)){
                throw new ErrorException("path not found",404,[
                    'path'=>$currentParamPath
                ]);
            }
            // 验证文件是否已存在
            $filePath = '../'.$currentParamPath.'/'.$currentParam['class_name'].'.php';
            if(file_exists($filePath)){
                throw new ErrorException("file already exists",500,[
                    'filepath'=>$filePath
                ]);
            }


            $appPath = App::getAppPath();
            $appPathArr = explode("\\", $appPath);
            $appFolder = $appPathArr[count($appPathArr)-1]?$appPathArr[count($appPathArr)-1]:$appPathArr[count($appPathArr)-2];
            $namespace = str_replace($appFolder, App::getNamespace(), $currentParam['path']);

            if ($key==="controller"){
                $pathArr = explode("\\", $namespace);
                $notArr = array(App::getNamespace(), $this->controller_layer);
                $url = "/";
                foreach ($pathArr as $pathItem){
                    if (!in_array($pathItem,$notArr)){
                        $url.=$pathItem."/";
                    }
                }
                $data['folder']=$url;
                $data['api_class_name']=lcfirst($currentParam['class_name']);
                $data['api_url']=$url.lcfirst($currentParam['class_name']);
            }else if ($key==="model" && !empty($currentParam['table'])){
                // 模型
                // 获取主键
                foreach ($currentParam['table'] as $item){
                    if ($item['main_key']==true){
                        $data['main_key.field'] = $item['field'];
                        $data['main_key.type'] = $item['type'];
                        $data['main_key.desc'] = $item['desc'];
                        break;
                    }
                }
            }

            $namespace = str_replace($appFolder, App::getNamespace(), $currentParam['path']);

            $data[$currentParam['name'].'.class_name']=$currentParam['class_name'];
            $data[$currentParam['name'].'.namespace']=$namespace;
            $data[$currentParam['name'].'.use_path']=$namespace."\\".$currentParam['class_name'];
            $data[$currentParam['name'].'.use_alias']=$currentParam['class_name'].ucwords($currentParam['name']);
        }

        // 字段过滤数据
        if (!empty($params['model']['table'])){
            $checkKeys = ['list','detail','add','edit'];
            foreach ($checkKeys as $checkKey){
                $itemArr = ['field'=>[],'withoutField'=>[]];
                foreach ($params['model']['table'] as $item){
                    if ($item[$checkKey]){
                        $itemArr['field'][]=$item['field'];
                    }else{
                        $itemArr['withoutField'][]=$item['field'];
                    }
                }
                $data[$checkKey.'.field']=implode(",", $itemArr['field']);
                $data[$checkKey.'.withoutField']=implode(",", $itemArr['withoutField']);
            }
            // 查询条件、验证规则
            $query_where='$where=[];'."\r\n";
            $query_annotation = "";
            $validate_rule = "["."\r\n";
            $validate_message = "["."\r\n";
            $validate_fields = [];
            $addRequireFields = [];
            foreach ($params['model']['table'] as $i=>$item){
                if ($item['query']){
                    $itemArr['field'][]=$item['field'];
                    $query_where.= '        if(!empty($param[\''.$item['field'].'\'])){'."\r\n";
                    $query_where.= '            $where[] = [\''.$item['field'].'\',\'=\',$param[\''.$item['field'].'\']];'."\r\n";
                    $query_where.= '        }'."\r\n";
                    $fh = empty($query_annotation)?'* ':'     * ';
                    $require = $item['not_null']==true?'true':'false';
                    $rn="";
                    if (($i+1)<count($params['model']['table'])){
                        $rn="\r\n";
                    }
                    $query_annotation.=$fh.'@Apidoc\Param("'.$item['field'].'",type="'.$item['type'].'",require='.$require.',desc="'.$item['desc'].'")'.$rn;
                }
                // 验证规则
                if (!empty($this->config['validate'])){
                    // 存在配置验证规则
                    if (!empty($item['validate']) && $this->config['validate']['rules']){
                        $rulesConfig = $this->config['validate']['rules'];
                        $currentRuleConfig = "";
                        foreach ($rulesConfig as $rule){
                            if ($rule['rule'] === $item['validate']){
                                $currentRuleConfig = $rule;
                                break;
                            }
                        }
                        if (!empty($currentRuleConfig)){
                            $validate_rule.='       \''.$item['field'].'\' => \''.$item['validate'].'\','."\r\n";
                            if (!empty($currentRuleConfig['message']) ){
                                if (is_array($currentRuleConfig['message']) && count($currentRuleConfig['message'])>0){
                                    foreach ($currentRuleConfig['message'] as $ruleKey=>$ruleMessage){
                                        if ($ruleKey=='0'){
                                            $ruleKeyStr = $item['field'];
                                        }else{
                                            $ruleKeyStr = Utils::replaceTemplate($ruleKey,$item);
                                        }
                                        $ruleMessageStr = Utils::replaceTemplate($ruleMessage,$item);
                                        $validate_message.='        \''.$ruleKeyStr.'\' => \''.$ruleMessageStr.'\','."\r\n";
                                    }
                                }else{
                                    $ruleMessageStr = Utils::replaceTemplate($currentRuleConfig['message'],$item);
                                    $validate_message.='        \''.$item['field'].'\' => \''.$ruleMessageStr.'\','."\r\n";
                                }
                            }
                            $validate_fields[]=$item['field'];
                            if($item['field'] !== $data['main_key.field']){
                                $addRequireFields[]=$item['field'];
                            }
                        }
                    }else if($item['not_null']){
                        $validate_fields[]=$item['field'];
                        if($item['field'] !== $data['main_key.field']){
                            $addRequireFields[]=$item['field'];
                        }
                        // 根据not_null生成必填
                        $validate_rule.='       \''.$item['field'].'\' => \'require\','."\r\n";
                        $validate_message.='        \''.$item['field'].'\' => \''.$item['field'].'不可为空\','."\r\n";
                    }
                }
            }
            $validate_rule.='   ];'."\r\n";
            $validate_message.='    ];'."\r\n";
            if (!empty($this->config['validate'])) {
                $data['validate.rule'] = $validate_rule;
                $data['validate.message'] = $validate_message;
                $data['validate.scene.edit'] = json_encode($validate_fields);
                $data['validate.scene.add'] =  json_encode($addRequireFields)=='[]'?'[\'\']':json_encode($addRequireFields);
                $data['validate.scene.delete'] = '[\'' . $data['main_key.field'] . '\']';
            }

            $data['query.where']=$query_where;
            $data['query.annotation']=$query_annotation;
        }
        return $data;

    }


    /**
     * 创建数据表
     * @return mixed
     */
    public function createModelTable($params,$title=""){
        $data = $params['table'];
        if (empty($title)){
            $title =$params['class_name'];
        }
        $driver = Config::get('database.default');
        $table_prefix=Config::get('database.connections.'.$driver.'.prefix');
        $table_name = $table_prefix.Utils::snake($params['class_name']);
        $table_data = '';
        $main_keys = '';
        foreach ($data as $item){
            $table_field="`".$item['field']."` ".$item['type'];
            if (!empty($item['length'])){
                $table_field.="(".$item['length'].")";
            }
            if ($item['main_key']){
                $main_keys.=$item['field'];
                $table_field.=" NOT NULL";
            }else if ($item['not_null']){
                $table_field.=" NOT NULL";
            }
            if ($item['incremental']==true){
                $table_field.=" AUTO_INCREMENT";
            }
            if (!empty($item['default'])){
                $table_field.=" DEFAULT '".$item['default']."'";
            }
            $table_field.=" COMMENT '".$item['desc']."',";
            $table_data.=$table_field;
        }
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
        $table_data
        PRIMARY KEY (`$main_keys`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='$title' AUTO_INCREMENT=1 ;";

        $tp_version = \think\facade\App::version();
        if (substr($tp_version, 0, 2) == '5.'){
            Db5::startTrans();
            try {
                Db5::query($sql);
                // 提交事务
                Db5::commit();
                return true;
            } catch (\Exception $e) {
                // 回滚事务
                Db5::rollback();
                return $e->getMessage();
            }
        }else{
            Db::startTrans();
            try {
                Db::query($sql);
                // 提交事务
                Db::commit();
                return true;
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $e->getMessage();
            }
        }

    }



}