<?php
namespace hg\apidoc;

use Doctrine\Common\Annotations\Reader;
use think\facade\Db;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\AddField;

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
        
        // 获取表字段
        $model = $this->getModel($methodAction,$modelClassName);
        if (!is_callable(array($model,'getTable'))){
            return false;
        }
        $table = $this->getTableDocument($model);

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

    public function getTableDocument($model)
    {
        $createSQL = Db::query("show create table " . $model->getTable())[0]['Create Table'];
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
            ];
        }
        return $fieldComment;
    }

}