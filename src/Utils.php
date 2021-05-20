<?php
declare(strict_types = 1);

namespace hg\apidoc;

use hg\apidoc\exception\ErrorException;
use think\facade\Config;
use think\response\Json;

class Utils
{
    /**
     * 统一返回json格式
     * @param int $code
     * @param string $msg
     * @param string $data
     * @return \think\response\Json
     */
    public static function showJson(int $code = 0, string $msg = "", $data = ""):Json
    {
        $res = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        return json($res);
    }

    /**
     * 过滤参数字段
     * @param $data
     * @param $fields
     * @param string $type
     * @return array
     */
    public function filterParamsField(array $data, $fields, string $type = "field"): array
    {
        if ($fields && strpos($fields, ',') !== false){
            $fieldArr = explode(',', $fields);
        }else{
            $fieldArr = [$fields];
        }

        $dataList = [];
        foreach ($data as $item) {
            if (!empty($item['name']) && in_array($item['name'], $fieldArr) && $type === 'field') {
                $dataList[] = $item;
            } else if (!(!empty($item['name']) && in_array($item['name'], $fieldArr)) && $type == "withoutField") {
                $dataList[] = $item;
            }
        }
        return $dataList;
    }

    /**
     * 读取文件内容
     * @param $fileName
     * @return false|string
     */
    public static function getFileContent(string $fileName): string
    {
        $content = "";
        if (file_exists($fileName)) {
            $handle  = fopen($fileName, "r");
            $content = fread($handle, filesize($fileName));
            fclose($handle);
        }
        return $content;
    }

    /**
     * 保存文件
     * @param $path
     * @param $str_tmp
     * @return bool
     */
    public static function createFile(string $path, string $str_tmp): bool
    {
        $pathArr = explode("/", $path);
        unset($pathArr[count($pathArr) - 1]);
        $dir = implode("/", $pathArr);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $fp = fopen($path, "w") or die("Unable to open file!");
        fwrite($fp, $str_tmp); //存入内容
        fclose($fp);
        return true;
    }

    /**
     * 删除文件
     * @param $path
     */
    public static function delFile(string $path)
    {
        $url = iconv('utf-8', 'gbk', $path);
        if (PATH_SEPARATOR == ':') { //linux
            unlink($path);
        } else {  //Windows
            unlink($url);
        }
    }

    /**
     * 将tree树形数据转成list数据
     * @param array $tree tree数据
     * @param string $childName 子节点名称
     * @return array  转换后的list数据
     */
    public function treeToList(array $tree, string $childName = 'children',string $key = "id",string $parentField = "parent")
    {
        $array = array();
        foreach ($tree as $val) {
            $array[] = $val;
            if (isset($val[$childName])) {
                $children = $this->treeToList($val[$childName], $childName);
                if ($children) {
                    $newChildren = [];
                    foreach ($children as $item) {
                        $item[$parentField] = $val[$key];
                        $newChildren[]      = $item;
                    }
                    $array = array_merge($array, $newChildren);
                }
            }
        }
        return $array;
    }

    /**
     * 根据一组keys获取所有关联节点
     * @param $tree
     * @param $keys
     */
    public function getTreeNodesByKeys(array $tree, array $keys, string $field = "id", string $childrenField = "children")
    {
        $list = $this->TreeToList($tree, $childrenField, "folder");
        $data = [];
        foreach ($keys as $k => $v) {
            $parent = !$k ? "" : $keys[$k - 1];
            foreach ($list as $item) {
                if (((!empty($item['parent']) && $item['parent'] === $parent) || empty($item['parent'])) && $item[$field] == $v) {
                    $data[] = $item;
                    break;
                }
            }
        }
        return $data;

    }

    /**
     * 替换模板变量
     * @param $temp
     * @param $data
     * @return string|string[]
     */
    public static function replaceTemplate(string $temp, array $data):string
    {
        $str = $temp;
        foreach ($data as $k => $v) {
            $key = '${' . $k . '}';
            if (strpos($str, $key) !== false) {
                $str = str_replace($key, $v, $str);
            }
        }
        return $str;
    }

    /**
     * 替换当前所选应用/版本的变量
     * @param $temp
     * @param $currentApps
     * @return string|string[]
     */
    public function replaceCurrentAppTemplate(string $temp,array $currentApps):string
    {
        $str = $temp;
        if (!empty($currentApps) && count($currentApps) > 0) {
            $data = [];
            for ($i = 0; $i <= 3; $i++) {
                if (isset($currentApps[$i])) {
                    $appItem = $currentApps[$i];
                    foreach ($appItem as $k => $v) {
                        $key        = 'app[' . $i . '].' . $k;
                        $data[$key] = $v;
                    }
                } else {
                    $appItem = $currentApps[0];
                    foreach ($appItem as $k => $v) {
                        $key        = 'app[' . $i . '].' . $k;
                        $data[$key] = "";
                    }
                }
            }
            $str = $this->replaceTemplate($str, $data);
        }
        return $str;
    }

    /**
     * 根据条件获取数组中的值
     * @param array $array
     * @param $query
     * @return mixed|null
     */
    public static function getArrayFind(array $array, $query)
    {
        $res = null;
        if (is_array($array)) {
            foreach ($array as $item) {
                if ($query($item)) {
                    $res = $item;
                    break;
                }
            }
        }
        return $res;
    }

    /**
     * 合并对象数组并根据key去重
     * @param string $name
     * @param mixed ...$array
     * @return array
     */
    public static function arrayMergeAndUnique(string $key = "name", ...$array):array
    {
        $mergeArr = [];
        foreach ($array as $k => $v) {
            $mergeArr = array_merge($mergeArr, $v);
        }
        $keys = [];
        foreach ($mergeArr as $k => $v) {
            $keys[] = $v[$key];
        }
        $uniqueKeys = array_flip(array_flip($keys));
        $newArray   = [];
        foreach ($uniqueKeys as $k => $v) {
            $newArray[] = $mergeArr[$k];
        }
        return $newArray;

    }

    /**
     * 初始化当前所选的应用/版本数据
     * @param $appKey
     */
    public function getCurrentApps(string $appKey):array
    {
        $config = Config::get('apidoc');
        if (!(!empty($config['apps']) && count($config['apps']) > 0)) {
            throw new ErrorException("no config apps", 500);
        }
        if (strpos($appKey, '_') !== false) {
            $keyArr = explode("_", $appKey);
        } else {
            $keyArr = [$appKey];
        }
        $currentApps = $this->getTreeNodesByKeys($config['apps'], $keyArr, 'folder', 'items');
        if (!$currentApps) {
            throw new ErrorException("appKey error", 412, [
                'appKey' => $appKey
            ]);
        }
        return $currentApps;

    }

    /**
     * 处理apps配置参数
     * @param array $apps
     * @return array
     */
    public function handleAppsConfig(array $apps):array
    {
        $appsConfig = [];
        foreach ($apps as $app) {
            if (!empty($app['password'])) {
                unset($app['password']);
                $app['hasPassword'] = true;
            }
            if (!empty($app['items']) && count($app['items']) > 0) {
                $app['items'] = $this->handleAppsConfig($app['items']);
            }
            $appsConfig[] = $app;
        }
        return $appsConfig;
    }


}