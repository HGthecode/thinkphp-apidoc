<?php

namespace hg\apidoc\generator;
use hg\apidoc\exception\ErrorException;
use hg\apidoc\generator\ParseTemplate;
use hg\apidoc\Utils;
use think\facade\App;
use think\facade\Config;
use think\facade\Db;
use think\Db as Db5;
use think\helper\Str;

class Index
{
    protected $config = [];

    protected $middlewares = [];

    public function __construct()
    {
        $this->config = Config::get('apidoc')?Config::get('apidoc'):Config::get('apidoc.');
    }

    public function create($params){
        $appKey = $params['form']['appKey'];
        $currentApps = (new Utils())->getCurrentApps($appKey);
        $generatorItem = $this->config['generator'][$params['index']];

        $checkParams = $this->checkFilesAndHandleParams($generatorItem,$params,$currentApps);
        $tplParams = $checkParams['tplParams'];
        // 注册中间件并执行before
        if (!empty($generatorItem['middleware']) && count($generatorItem['middleware'])){
            foreach ($generatorItem['middleware'] as $middleware) {
                $instance = new $middleware;
                $this->middlewares[] = $instance;
                if (method_exists($instance, 'before')) {
                    $middlewareRes = $instance->before($tplParams);
                    if (!empty($middlewareRes)){
                        $tplParams = $middlewareRes;
                    }
                }
            }
        }

        $this->createModels($checkParams['createModels'],$tplParams);

        $this->createFiles($checkParams['createFiles'],$tplParams);


        // 执行after
        if (count($this->middlewares)){
            foreach ($this->middlewares as $middleware) {
                if (method_exists($instance, 'after')) {
                    $instance->after($tplParams);
                }
            }
        }
        return $tplParams;
    }

    /**
     * 验证文件及处理模板数据
     * @param $generatorItem
     * @param $params
     * @param $currentApps
     * @return array
     */
    protected function checkFilesAndHandleParams($generatorItem,$params,$currentApps){
        // 组成模板参数
        $tplParams=[
            'form'=>$params['form'],
            'tables'=>$params['tables'],
            'app'=>$currentApps
        ];
        $createFiles = [];
        if (!empty($params['files']) && count($params['files'])>0) {
            $files = $params['files'];
            foreach ($files as $file) {
                $fileConfig = Utils::getArrayFind($generatorItem['files'], function ($item) use ($file) {
                    if ($file['name'] === $item['name']) {
                        return true;
                    }
                    return false;
                });

                $filePath = (new Utils())->replaceCurrentAppTemplate($fileConfig['path'], $currentApps);
                if (!empty($fileConfig['namespace'])) {
                    $fileNamespace = (new Utils())->replaceCurrentAppTemplate($fileConfig['namespace'], $currentApps);
                } else {
                    $fileNamespace = $filePath;
                }
                $fileNamespaceEndStr = substr($fileNamespace, -1);
                if ($fileNamespaceEndStr == '\\') {
                    $fileNamespace = substr($fileNamespace, 0, strlen($fileNamespace) - 1);
                }
                $template = (new Utils())->replaceCurrentAppTemplate($fileConfig['template'], $currentApps);
                $tplParams[$file['name']] = [
                    'class_name' => $file['value'],
                    'path' => $filePath,
                    'namespace' => $fileNamespace,
                    'template' => $template
                ];

                // 验证模板是否存在
                $templatePath =Utils::formatPath( App::getRootPath() . $template,"/");
                if (is_readable($templatePath) == false) {
                    throw new ErrorException("template not found", 412, [
                        'template' => $template
                    ]);
                }
                // 验证是否已存在生成的文件
                $fileFullPath = Utils::formatPath(App::getRootPath() . $filePath, "/");
                $type = "folder";
                if (strpos($fileFullPath, '.php') !== false) {
                    // 路径为php文件，则验证文件是否存在
                    if (is_readable($fileFullPath) == false) {
                        throw new ErrorException("file not exists", 412, [
                            'filepath' => $filePath
                        ]);
                    }
                    $type = "file";
                } else {
                    $fileName = !empty($file['value']) ? $file['value'] : "";
                    $fileFullPath = $fileFullPath . "/" . $fileName . ".php";
                    if (is_readable($fileFullPath)) {
                        throw new ErrorException("file already exists", 412, [
                            'filepath' => Utils::formatPath($filePath) . $fileName . ".php"
                        ]);
                    }
                }
                $createFiles[] = [
                    'fileFullPath' => $fileFullPath,
                    'template' => $template,
                    'templatePath'=>$templatePath,
                    'type' => $type
                ];


            }
        }

        $createModels = $this->checkModels($generatorItem,$tplParams);
        return [
            'tplParams'=>$tplParams,
            'createFiles'=>$createFiles,
            'createModels' =>$createModels
        ];
    }

    /**
     * 验证模型及表
     * @param $generatorItem
     * @param $tplParams
     * @return array
     */
    protected function checkModels($generatorItem,$tplParams){
        $res="";
        $tabls = $tplParams['tables'];
        $createModels = [];
        $tp_version = \think\facade\App::version();
        if (!empty($tabls) && count($tabls)){
            foreach ($tabls as $k=>$table) {
                $tableConfig = $generatorItem['table'];
                $fileFullPath="";
                if (!empty($table['model_name'])){
                    $namespace = $tableConfig['items'][$k]['namespace'];
                    $template = $tableConfig['items'][$k]['template'];
                    $path = $tableConfig['items'][$k]['path'];

                    // 验证模板是否存在
                    $templatePath = Utils::formatPath(App::getRootPath() . $template,"/");
                    if (is_readable($templatePath) == false) {
                        throw new ErrorException("template not found", 412, [
                            'template' => $template
                        ]);
                    }
                    $tplParams['tables'][$k]['class_name'] =$table['model_name'];
                    // 验证模型是否已存在
                    $fileName = $table['model_name'];
                    $fileFullPath = Utils::formatPath(App::getRootPath().$path) . "/" . $fileName . ".php";
                    if (is_readable($fileFullPath)) {
                        throw new ErrorException("file already exists", 412, [
                            'filepath' => Utils::formatPath($path) . "/" . $fileName . ".php"
                        ]);
                    }
                }
                // 验证表是否存在
                if ($table['table_name']){
                    $driver = Config::get('database.default');
                    $table_prefix=Config::get('database.connections.'.$driver.'.prefix');
                    $table_name = $table_prefix.$table['table_name'];

                    if (substr($tp_version, 0, 2) == '5.'){
                        $isTable = Db5::query('SHOW TABLES LIKE '."'".$table_name."'");
                    }else{
                        $isTable = Db::query('SHOW TABLES LIKE '."'".$table_name."'");
                    }
                    if ($isTable){
                        throw new ErrorException("datatable already exists", 412, [
                            'table' => $table_name
                        ]);
                    }
                }
                $createModels[]=[
                    'namespace'=>$namespace,
                    'template'=>$template,
                    'path'=>$path,
                    'templatePath' =>$templatePath,
                    'table'=>$table,
                    'fileFullPath'=>$fileFullPath
                ];
            }
        }
        return $createModels;

    }

    /**
     * 创建文件
     * @param $createFiles
     * @param $tplParams
     * @return mixed
     */
    protected function createFiles($createFiles,$tplParams){

        if (!empty($createFiles) && count($createFiles)>0){
            foreach ($createFiles as $fileItem) {
                $html = (new ParseTemplate())->compile($fileItem['templatePath'],$tplParams);
                if ($fileItem['type'] === "file"){
                    // 路径为文件，则添加到该文件
                    $pathFileContent = Utils::getFileContent($fileItem['fileFullPath']);
                    $content = $pathFileContent."\r\n".$html;
                    Utils::createFile($fileItem['fileFullPath'],$content);
                }else{
                    Utils::createFile($fileItem['fileFullPath'],$html);
                }
            }
        }
        return $tplParams;
    }

    /**
     * 创建模型文件
     * @param $createModels
     * @param $tplParams
     */
    protected function createModels($createModels,$tplParams){
        if (!empty($createModels) && count($createModels)>0){
            foreach ($createModels as $k=>$item) {
                $table = $item['table'];
                if (!empty($table['table_name'])){
                    $res =  $this->createTable($table);
                }
                if (!empty($table['model_name'])){
                    $tplParams['tables'][$k]['class_name'] =$table['model_name'];
                    $html = (new ParseTemplate())->compile($item['templatePath'],$tplParams);
                    Utils::createFile($item['fileFullPath'],$html);
                }

            }
        }
    }

    /**
     * 创建数据表
     * @return mixed
     */
    protected function createTable($table){
        $datas = $table['datas'];
        $comment= "";
        if (!empty($table['table_comment'])){
            $comment =$table['table_comment'];
        }
        $driver = Config::get('database.default');
        $table_prefix=Config::get('database.connections.'.$driver.'.prefix');
        $table_name = $table_prefix.$table['table_name'];
        $table_data = '';
        $main_keys = '';
        foreach ($datas as $k=>$item){
            if (!empty($item['not_table_field'])){
                continue;
            }
            $table_field="`".$item['field']."` ".$item['type'];
            if (!empty($item['length'])){
                $table_field.="(".$item['length'].")";
            }

            if (!empty($item['main_key'])){
                $main_keys.=$item['field'];
                $table_field.=" NOT NULL";
            }else if (!empty($item['not_null'])){
                $table_field.=" NOT NULL";
            }
            if (!empty($item['incremental']) && !empty($item['main_key'])){
                $table_field.=" AUTO_INCREMENT";
            }
            if (!empty($item['default']) || $item['default']=="0"){
                $table_field.=" DEFAULT '".$item['default']."'";
            }else if (!empty($item['main_key']) && !$item['not_null']){
                $table_field.=" DEFAULT NULL";
            }
            $fh = $k < (count($datas)-1)?",":"";
            $table_field.=" COMMENT '".$item['desc']."'".$fh;
            $table_data.=$table_field;
        }
        $primaryKey = "";
        if (!empty($main_keys)){
            $table_data.=",";
            $primaryKey = "PRIMARY KEY (`$main_keys`)";
        }
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
        $table_data
        $primaryKey
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='$comment' AUTO_INCREMENT=1 ;";

        $tp_version = \think\facade\App::version();
        if (substr($tp_version, 0, 2) == '5.'){
            Db5::startTrans();
            try {
                Db5::query($sql);
                Db5::commit();
                return true;
            } catch (\Exception $e) {
                Db5::rollback();
                return $e->getMessage();
            }
        }else{
            Db::startTrans();
            try {
                Db::query($sql);
                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                throw new ErrorException("datatable create error", 412, [
                    'table' => $table_name,
                    'message'=>$e->getMessage(),
                    'sql'=>$sql
                ]);
            }
        }


    }
}