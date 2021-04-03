<?php
namespace hg\apidoc;

use Doctrine\Common\Annotations\Reader;
use think\facade\Db;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\AddField;
use think\helper\Str;

class ParseModel
{
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }


    public function renderModel($path){
        $modelClassArr = explode("\\", $path);
        $modelActionName = $modelClassArr[count($modelClassArr)-1];
        $modelClassName = $modelClassArr[count($modelClassArr)-2];
        unset($modelClassArr[count($modelClassArr)-1]);
        $modelClassPath = implode("\\", $modelClassArr);
        $classReflect = new \ReflectionClass($modelClassPath);
        $modelActionName = trim ( $modelActionName );
        $methodAction = $classReflect->getMethod($modelActionName);
        // 获取所有模型属性
        $propertys = $classReflect->getDefaultProperties();

        // 获取表字段
        $model = $this->getModel($methodAction,$modelClassName);
        if (!is_callable(array($model,'getTable'))){
            return false;
        }
        $table = $this->getTableDocument($model,$propertys);

        // 模型注释-field
        if ($fieldAnnotations = $this->reader->getMethodAnnotation($methodAction,Field::class)) {
            $table = (new Utils())->filterParamsField($table,$fieldAnnotations->value,'field');
        }
        // 模型注释-withoutField
        if ($fieldAnnotations = $this->reader->getMethodAnnotation($methodAction,WithoutField::class)) {
            $table = (new Utils())->filterParamsField($table,$fieldAnnotations->value,'withoutField');
        }
        // 模型注释-addField
        if ($annotations = $this->reader->getMethodAnnotations($methodAction)) {
            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof AddField:
                        $param=[
                            "name"=>$annotation->value,
                            "desc"=>$annotation->desc,
                            "require"=>$annotation->require,
                            "type"=>$annotation->type,
                            "default"=>$annotation->default
                        ];
                        $table[]=$param;
                    break;
                }
            }
        }
        return $table;
    }

    public function getModel($method,$modelClassName){
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

    protected function getIncludeClassName($mainClass, $class)
    {

            $classReflect = new \ReflectionClass($mainClass);
            $possibleClass = $classReflect->getNamespaceName() . "\\" . $class;
            if (class_exists($possibleClass)) {
                return $possibleClass;
            } else {
                return "";
            }
    }

    public function getTableDocument($model,$propertys)
    {

        $createSQL = Db::query("show create table " . $model->getTable())[0]['Create Table'];
        preg_match_all("#[^KEY]`(.*?)` (.*?) (.*?),\n#", $createSQL, $matches);
        $fields = $matches[1];
        $types = $matches[2];
        $contents = $matches[3];
        $fieldComment = [];
        //组织注释
        for ($i = 0; $i < count($matches[0]); $i++) {
            $key = $fields[$i];
            $type = $types[$i];
            $default="";
            $require="0";
            $desc ="";
            $content=$contents[$i];
            if (strpos($type,'(`') !== false){
                continue;
            }
            if (strpos($content,'COMMENT') !== false){
                // 存在字段注释
                preg_match_all("#COMMENT\s*'(.*?)'#", $content, $edscs);
                if (!empty($edscs[1]) && !empty($edscs[1][0]))
                    $desc=$edscs[1][0];
            }
            if (strpos($content,'DEFAULT') !== false){
                // 存在字段默认值
                preg_match_all("#DEFAULT\s*'(.*?)'#", $content, $defaults);
                if (!empty($defaults[1]) && !empty($defaults[1][0]))
                    $default=$defaults[1][0];
            }

            if (strpos($content,'NOT NULL') !== false){
                // 必填字段
                $require="1";
            }

            $name = $key;
            // 转换字段名为驼峰命名（用于输出）
            if (isset($propertys['convertNameToCamel']) && $propertys['convertNameToCamel'] === true) {
                $name = Str::camel($key);
            }
            $fieldComment[] = [
                "name"=>$name,
                "type"=>$type,
                "desc"=>$desc,
                "default"=>$default,
                "require"=>$require,
            ];
        }
        return $fieldComment;
    }

}