<?php


namespace hg\apidoc;
//use think\Db;
use think\facade\Db;

class Parser
{
    protected  $config = [];

    /**
     * 架构方法 设置参数
     * @param  array $config 配置参数
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 解析class类的注释
     * @param string $doc
     * @return array
     */
    public function parseClass($doc = '') {
        if ($doc == '') {
            return false;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return false;
        $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return false;
        $res = $this->parseClassLines ( $lines [1] );

        return $res;
    }

    /**
     * 解析class类的注释，将每条字符串，解析成key,value对象
     * @param $lines
     * @return array|bool
     */
    private function parseClassLines($lines) {
        $desc = [];
        foreach ( $lines as $line ) {
            $line = trim ( $line );
            if (empty ( $line )){
                return false; // Empty line
            }
            if (strpos ( $line, '@' ) === 0) {
                if (strpos ( $line, ' ' ) > 0) {
                    // Get the parameter name
                    $param = substr ( $line, 1, strpos ( $line, ' ' ) - 1 );
                    $value = substr ( $line, strlen ( $param ) + 2 ); // Get the value
                } else {
                    $param = substr ( $line, 1 );
                    $value = '';
                }

                    $desc[$param]=$value;

            }

        }
        return $desc;
    }


    /**
     * 解析控制器方法的注释
     * @param string $doc
     * @return array
     */
    public function parseAction($doc = '') {
        if ($doc == '') {
            return false;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return false;
        $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return false;
        $res = $this->parseActionLines ( $lines [1] );

        return $res;
    }


    /**
     * 解析方法的注释，将每条字符串，解析成key,value对象，并处理
     * @param $lines
     * @return array|bool
     */
    private function parseActionLines($lines) {
        $desc = [];
        foreach ( $lines as $line ) {
            $line = trim ( $line );
            if (!empty ( $line )){
                if (strpos ( $line, '@' ) === 0) {
                    if (strpos($line, ' ') > 0) {
                        // Get the parameter name
                        $param = substr($line, 1, strpos($line, ' ') - 1);
                        $value = substr($line, strlen($param) + 2); // Get the value
                    } else {
                        $param = substr($line, 1);
                        $value = '';
                    }

                    if ($param == 'param') {
                        $valueObj = $this->formatParam($value);
                        if (!empty($valueObj['params']) && empty($valueObj['name'])) {
                            // 只配置参数，没配置name则直接值为参数
                            if (is_array($valueObj["params"]) && count($valueObj["params"]) > 0) {
                                // 数组则遍历出来
                                foreach ($valueObj["params"] as $paramItem) {
                                    $desc [$param][] = $paramItem;
                                }
                            } else {
                                $desc [$param][] = $valueObj["params"];
                            }

                        } else {
                            $desc [$param][] = $valueObj;
                        }

                    } else if ($param == 'return') {
                        $valueObj = $this->formatReturn($value);
                        if (!empty($valueObj['params']) && empty($valueObj['name'])) {
                            // 只配置参数，没配置name则直接值为参数
                            if (is_array($valueObj["params"]) && count($valueObj["params"]) > 0) {
                                // 数组则遍历出来
                                foreach ($valueObj["params"] as $paramItem) {
                                    $desc [$param][] = $paramItem;
                                }
                            } else {
                                $desc [$param][] = $valueObj["params"];
                            }
                        } else {
                            $desc [$param][] = $valueObj;
                        }

                    } else if ($param == 'header') {
                        $valueObj = $this->formatHeaders($value);
                        $desc [$param][] = $valueObj;
                    } else if ($param == 'addField'){
                        // 模型指定添加的字段
                        $valueObj = $this->formatHeaders($value);
                        $desc [$param][] = $valueObj;
                    }else{
                        $desc[$param]=$value;
                    }

                }
            }


        }
        return $desc;
    }


    // 处理Headers的解析
    private function formatHeaders($string) {
        $string = $string." ";
        if(preg_match_all('/(\w+):(.*?)[\s\n]/s', $string, $meatchs)){
            $param = [];
            foreach ($meatchs[1] as $key=>$value){
                $paramKey = $meatchs[1][$key];
                $value = $meatchs[2][$key];
                $param[$paramKey] =$value;
            }
            return $param;
        }else{
            return ''.$string;
        }
    }

    // 处理Param的解析
    private function formatParam($string) {
        $string = $string." ";
        if(preg_match_all('/(\w+):(.*?)[\s\n]/s', $string, $meatchs)){
            $param = [];
            foreach ($meatchs[1] as $key=>$value){
                $paramKey = $meatchs[1][$key];
                $value = $meatchs[2][$key];
                if ($paramKey == "params"){
                    // 处理对象类型
                    $value = $this->parseObjectLine($value);
                }else if ($paramKey == "ref") {
                    // 处理引用
                    $value = $this->parseRefLine($value,"param");
                    $paramKey="params";
                }else if ($paramKey == "field" && !empty($param["params"])) {
                    // 只取模型指定字段
                    $param["params"] = $this->filterModelTableField($param["params"],$value,"field");
                }else if ($paramKey == "withoutField" && !empty($param["params"])) {
                    // 排除模型指定字段
                    $param["params"] = $this->filterModelTableField($param["params"],$value,"withoutField");
                }
                    $param[$paramKey] =$value;
            }
            return $param;
        }else{
            return ''.$string;
        }
    }


    // 处理Return的解析
    private function formatReturn($string) {
        $string = $string." ";
        if(preg_match_all('/(\w+):(.*?)[\s\n]/s', $string, $meatchs)){
            $param = [];
            foreach ($meatchs[1] as $key=>$value){
                $paramKey = $meatchs[1][$key];
                $value = $meatchs[2][$key];
                if ($paramKey == "params"){
                    // 处理对象类型
                    $value = $this->parseObjectLine($value);
                }else if ($paramKey == "ref") {
                    // 处理引用
                    $value = $this->parseRefLine($value,"return");
                    if (!empty($value) && is_array($value) && count($value)===1){
                        if (!empty($value[0]) && !empty($value[0]['params'])){
                            $value=$value[0]['params'];
                        }
                    }
                    $paramKey="params";
                }else if ($paramKey == "field" && !empty($param["params"])) {
                    // 只取模型指定字段
                    $param["params"] = $this->filterModelTableField($param["params"],$value,"field");
                }else if ($paramKey == "withoutField" && !empty($param["params"])) {
                    // 排除模型指定字段
                    $param["params"] = $this->filterModelTableField($param["params"],$value,"withoutField");
                }
                $param[$paramKey] =$value;
            }
            return $param;
        }else{
            return ''.$string;
        }
    }



    // 解析param参数为对象类型
    public function parseObjectLine($string){
        $string = trim ( $string );
        if (empty ( $string )){
            return false; // Empty line
        }
        $string = $string.",";
        if(preg_match_all('/(\w+):(.*?),/s', $string, $meatchs)){
            $param = [];
            foreach ($meatchs[1] as $key=>$value){
                $paramKey = $meatchs[1][$key];
                $value = $meatchs[2][$key];
                $param[] =array("name"=>$paramKey,"type"=>$value);
            }
            return $param;
        }else{
            return ''.$string;
        }

    }

    // 解析ref引用的数据,server、model、引用定义
    public function parseRefLine($string,$paramKey=""){
        $string = trim ( $string );
        if (empty ( $string )){
            return false; // Empty line
        }
        $value = $string;
        if (strpos($string,'app\\') !== false && strpos($string,'model\\') === false){
            // 引用服务
           $value = $this->parseServer($string,$paramKey);
           if (!empty($paramKey) && !empty($value) && !empty($value[$paramKey])){
               // 存在指定取值的key,去server注释中指定的值
               $value = $value[$paramKey];
           }

        }else if (strpos($string,'model\\') !== false){
            // 引用模型
            $value = $this->parseModel($string);
        }else if (strpos($string,'definitions\\') !== false){
            // 引用定义
            $value = $this->parseDefinitions($string);
            if (!empty($paramKey) && !empty($value) && !empty($value[$paramKey])){
                // 存在指定取值的key,去server注释中指定的值
                $value = $value[$paramKey];
            }
        }
        return $value;
    }

    // 解析服务的注释
    public function parseServer($path,$paramKey){

        $modelClassArr = explode("\\", $path);
        $modelActionName = $modelClassArr[count($modelClassArr)-1];
        $modelClassName = $modelClassArr[count($modelClassArr)-2];
        unset($modelClassArr[count($modelClassArr)-1]);
        $modelClassPath = implode("\\", $modelClassArr);
        $classReflect = new \ReflectionClass($modelClassPath);
        $modelActionName = trim ( $modelActionName );
        $methodAction = $classReflect->getMethod($modelActionName);
        $doc_str = $methodAction->getDocComment();
        $action_doc = $this->parseParam($doc_str);
        return $action_doc;
    }

    // 解析模型的注释
    public function parseModel($path){
        $modelClassArr = explode("\\", $path);
        $modelActionName = $modelClassArr[count($modelClassArr)-1];
        $modelClassName = $modelClassArr[count($modelClassArr)-2];
        unset($modelClassArr[count($modelClassArr)-1]);
        $modelClassPath = implode("\\", $modelClassArr);
        $classReflect = new \ReflectionClass($modelClassPath);
        $modelActionName = trim ( $modelActionName );
        $methodAction = $classReflect->getMethod($modelActionName);
        //获取模型方法的注释
        $doc_str = $methodAction->getDocComment();
        //解析注释
        $action_doc = $this->parseParam($doc_str);
        // 获取表字段
        $model = $this->getModel($methodAction,$modelClassName);
        $table = $this->getTableDocument($model);

        //过滤field
        if (!empty($action_doc) && !empty($action_doc['field'])){
            $table=$this->filterModelTableField($table,$action_doc['field'],"field");
        }else if (!empty($action_doc) && !empty($action_doc['withoutField'])){
            $table=$this->filterModelTableField($table,$action_doc['withoutField'],"withoutField");
        }
        if (!empty($action_doc) && !empty($action_doc['addField'])){
            $table=array_merge($table, $action_doc['addField']);
        }

        return $table;
    }



    // 获取模型
    private function getModel($method,$modelClassName){
        if (!empty($method->class)){
            $relationModelClass = $this->getIncludeClassName($method->class, $modelClassName);
            if ($relationModelClass) {
                $modelInstance = new $relationModelClass();
                return $modelInstance;
            } else {
                return null;
            }
        }else{
            return null;
        }

    }

    // 过滤模型字段
    public function filterModelTableField($params,$keys,$type="field"){
        $modelParams=[];
        $fieldArr=explode(',' , $keys);
        foreach ($params as $modelParam){
            if (!empty($modelParam['name']) && in_array($modelParam['name'], $fieldArr) && $type =="field"){
                // 取指定字段
                $modelParams[]=$modelParam;
            }else if (!(!empty($modelParam['name']) && in_array($modelParam['name'], $fieldArr)) && $type =="withoutField"){
                // 排除指定字段
                $modelParams[]=$modelParam;
            }
        }
        return $modelParams;
    }


    // 解析定义的注释
    public function parseDefinitions($path){
        $modelClassArr = explode("\\", $path);
        $modelActionName = $modelClassArr[count($modelClassArr)-1];
        $definitionsPath = !empty($this->config['definitions'])?$this->config['definitions']:"hg\apidoc\Definitions";
        $classReflect = new \ReflectionClass($definitionsPath);
        $modelActionName = trim ( $modelActionName );
        $methodAction = $classReflect->getMethod($modelActionName);
        $doc_str = $methodAction->getDocComment();
        $action_doc = $this->parseParam($doc_str);
        return $action_doc;
    }

    /**
     * 解析参数的注释，server的注释解析
     * @param string $doc
     * @return array
     */
    public function parseParam($doc = '') {
        if ($doc == '') {
            return false;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return false;
        $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return false;
        $res = $this->parseActionLines ( $lines [1] );

        return $res;
    }



    /**
     * 根据模型获取表的注释
     * @param Model $model
     * @return array
     */
    public function getTableDocument($model)
    {
        $createSQL = Db::query("show create table " . $model->getTable())[0]['Create Table'];
//        preg_match_all("#`(.*?)`(.*?) COMMENT\s*'(.*?)',#", $createSQL, $matches);
        preg_match_all("#`(.*?)`(.*?),#", $createSQL, $matches);
        $fields = $matches[1];
        $types = $matches[2];
        $fieldComment = [];
        //组织注释
        for ($i = 0; $i < count($matches[0]); $i++) {
            $key = $fields[$i];

            $typeString = $types[$i];
            $typeString = trim ( $typeString );
            $typeArr = explode(' ' , $typeString);
            $type = $typeArr[0];
            $default="";
            $require="0";
            $desc ="";
            if (strpos($typeString,'COMMENT') !== false){
                // 存在字段注释
                preg_match_all("#COMMENT\s*'(.*?)'#", $typeString, $edscs);
                if (!empty($edscs[1]) && !empty($edscs[1][0]))
                $desc=$edscs[1][0];
            }
            if (strpos($typeString,'DEFAULT') !== false){
                // 存在字段默认值
                preg_match_all("#DEFAULT\s*'(.*?)'#", $typeString, $defaults);
                if (!empty($defaults[1]) && !empty($defaults[1][0]))
                $default=$defaults[1][0];
            }

            if (strpos($typeString,'NOT NULL') !== false){
                // 必填字段
                $require="1";
            }

            $fieldComment[] = [
                "name"=>$key,
                "type"=>$type,
                "desc"=>$desc,
                "default"=>$default,
                "require"=>$require,
//                "str"=>$createSQL
            ];
        }
        return $fieldComment;
    }


    /**
     * 获取类文件的内容
     * @param $className
     * @return mixed
     * @throws \Exception
     */
    protected function getClassFileContent($className)
    {
        if (class_exists($className)) {
            $classReflect = new \ReflectionClass($className);
        } else {
            throw new \Exception("类不存在", '1');
        }
        if (!isset($this->classFileMaps[$className])) {
            $this->classFileMaps[$className] = file_get_contents($classReflect->getFileName());
        }
        return $this->classFileMaps[$className];
    }

    public function getIncludeClassName($mainClass, $class)
    {
        $classFile = $this->getClassFileContent($mainClass);
        $pattern = "/use\s*(app.*?\\\\$class)/";
        if (preg_match($pattern, $classFile, $matches)) {
            return $matches[1];
        } else {
            $classReflect = new \ReflectionClass($mainClass);
            $possibleClass = $classReflect->getNamespaceName() . "\\" . $class;
            if (class_exists($possibleClass)) {
                return $possibleClass;
            } else {
                return "";
            }
        }
    }



}