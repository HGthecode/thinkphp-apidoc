<?php
declare(strict_types = 1);

namespace hg\apidoc\parseApi;

use Doctrine\Common\Annotations\Reader;
use think\Db as Db5;
use think\facade\Db;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\AddField;
use think\helper\Str;
use hg\apidoc\Utils;

class ParseModel
{
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * 生成模型数据
     * @param string $path
     * @return array|false
     * @throws \ReflectionException
     */
    public function renderModel(string $path)
    {
        $modelClassArr   = explode("\\", $path);
        $modelActionName = $modelClassArr[count($modelClassArr) - 1];
        $modelClassName  = $modelClassArr[count($modelClassArr) - 2];
        unset($modelClassArr[count($modelClassArr) - 1]);
        $modelClassPath  = implode("\\", $modelClassArr);
        $classReflect    = new \ReflectionClass($modelClassPath);
        $modelActionName = trim($modelActionName);
        $methodAction    = $classReflect->getMethod($modelActionName);
        // 获取所有模型属性
        $propertys = $classReflect->getDefaultProperties();

        // 获取表字段
        $model = $this->getModel($methodAction, $modelClassName);
        if (!is_callable(array($model, 'getTable'))) {
            return false;
        }
        $table = $this->getTableDocument($model, $propertys);

        // 模型注释-field
        if ($fieldAnnotations = $this->reader->getMethodAnnotation($methodAction, Field::class)) {
            $table = (new Utils())->filterParamsField($table, $fieldAnnotations->value, 'field');
        }
        // 模型注释-withoutField
        if ($fieldAnnotations = $this->reader->getMethodAnnotation($methodAction, WithoutField::class)) {
            $table = (new Utils())->filterParamsField($table, $fieldAnnotations->value, 'withoutField');
        }
        // 模型注释-addField
        if ($annotations = $this->reader->getMethodAnnotations($methodAction)) {
            foreach ($annotations as $annotation) {
                switch (true) {
                    case $annotation instanceof AddField:
                        $param         = [
                            "name"    => "",
                            "desc"    => $annotation->desc,
                            "require" => $annotation->require,
                            "type"    => $annotation->type,
                            "default" => $annotation->default
                        ];
                        $children      = $this->handleParamValue($annotation->value);
                        $param['name'] = $children['name'];
                        if (count($children['params']) > 0) {
                            $param['children'] = $children['params'];
                        }
                        $isExists = false;
                        $newTable = [];
                        foreach ($table as $item) {
                            if ($param['name'] === $item['name']) {
                                $isExists   = true;
                                $newTable[] = $param;
                            } else {
                                $newTable[] = $item;
                            }
                        }
                        $table = $newTable;
                        if (!$isExists) {
                            $table[] = $param;
                        }
                        break;
                }
            }
        }
        return $table;
    }

    /**
     * 处理字段参数
     * @param $values
     * @return array
     */
    protected function handleParamValue($values): array
    {
        $name   = "";
        $params = [];
        if (!empty($values) && is_array($values) && count($values) > 0) {
            foreach ($values as $item) {
                if (is_string($item)) {
                    $name = $item;
                } else if (is_object($item)) {
                    $param         = [
                        "name"    => "",
                        "type"    => $item->type,
                        "desc"    => $item->desc,
                        "default" => $item->default,
                        "require" => $item->require,
                    ];
                    $children      = $this->handleParamValue($item->value);
                    $param['name'] = $children['name'];
                    if (count($children['params']) > 0) {
                        $param['children'] = $children['params'];
                    }
                    $params[] = $param;
                }
            }
        } else {
            $name = $values;
        }
        return ['name' => $name, 'params' => $params];
    }

    /**
     * 获取模型实例
     * @param $method
     * @param $modelClassName
     * @return mixed|null
     */
    public function getModel($method, $modelClassName)
    {
        if (!empty($method->class)) {
            $relationModelClass = $this->getIncludeClassName($method->class, $modelClassName);
            if ($relationModelClass) {
                $modelInstance = new $relationModelClass();
                return $modelInstance;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    /**
     * 获取模型类
     * @param $mainClass
     * @param $class
     * @return string
     * @throws \ReflectionException
     */
    protected function getIncludeClassName($mainClass, $class)
    {
        $classReflect  = new \ReflectionClass($mainClass);
        $possibleClass = $classReflect->getNamespaceName() . "\\" . $class;
        if (class_exists($possibleClass)) {
            return $possibleClass;
        } else {
            return "";
        }
    }

    /**
     * 获取模型注解数据
     * @param $model
     * @param $propertys
     * @return array
     */
    public function getTableDocument($model,array $propertys):array
    {

        $tp_version = \think\facade\App::version();
        if (substr($tp_version, 0, 2) == '5.'){
            $createSQL = Db5::query("show create table " . $model->getTable())[0]['Create Table'];
        }else{
            $createSQL = Db::query("show create table " . $model->getTable())[0]['Create Table'];
        }
//        $createSQL = Db::query("show create table " . $model->getTable())[0]['Create Table'];
        preg_match_all("#[^KEY]`(.*?)` (.*?) (.*?),\n#", $createSQL, $matches);
        $fields       = $matches[1];
        $types        = $matches[2];
        $contents     = $matches[3];
        $fieldComment = [];
        //组织注释
        for ($i = 0; $i < count($matches[0]); $i++) {
            $key     = $fields[$i];
            $type    = $types[$i];
            $default = "";
            $require = "0";
            $desc    = "";
            $content = $contents[$i];
            if (strpos($type, '(`') !== false) {
                continue;
            }
            if (strpos($content, 'COMMENT') !== false) {
                // 存在字段注释
                preg_match_all("#COMMENT\s*'(.*?)'#", $content, $edscs);
                if (!empty($edscs[1]) && !empty($edscs[1][0])){
                    $desc = Utils::getLang($edscs[1][0]);

                }

            }
            if (strpos($content, 'DEFAULT') !== false) {
                // 存在字段默认值
                preg_match_all("#DEFAULT\s*'(.*?)'#", $content, $defaults);
                $default = $defaults[1] && is_array($defaults[1])?$defaults[1][0]:"";
            }

            if (strpos($content, 'NOT NULL') !== false) {
                // 必填字段
                $require = "1";
            }

            $name = $key;
            // 转换字段名为驼峰命名（用于输出）
            if (isset($propertys['convertNameToCamel']) && $propertys['convertNameToCamel'] === true) {
                $name = Str::camel($key);
            }
            $fieldComment[] = [
                "name"    => $name,
                "type"    => $type,
                "desc"    => $desc,
                "default" => $default,
                "require" => $require,
            ];
        }
        return $fieldComment;
    }

}