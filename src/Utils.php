<?php
declare(strict_types = 1);

namespace hg\apidoc;

use hg\apidoc\exception\ErrorException;
use think\facade\Config;
use think\facade\Lang;
use think\response\Json;

class Utils
{
    protected static $snakeCache = [];
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
     * 根据条件获取数组中的值
     * @param array $array
     * @param $query
     * @return mixed|null
     */
    public static function getArrayFindIndex(array $array, $query)
    {
        $res = null;
        if (is_array($array)) {
            foreach ($array as $k=>$item) {
                if ($query($item)) {
                    $res = $k;
                    break;
                }
            }
        }
        return $res;
    }

    /**
     * 查询符合条件的数组
     * @param array $array
     * @param $query
     * @return array
     */
    public static function getArraybyQuery(array $array, $query)
    {
        $res = [];
        if (is_array($array)) {
            foreach ($array as $item) {
                if ($query($item)) {
                    $res[] = $item;
                }
            }
        }
        return $res;
    }

    /**
     * 对象转为数组
     * @param $object
     * @return mixed
     */
    public static function objectToArray($object) {
        $object =  json_decode( json_encode($object),true);
        return  $object;
    }

    /**
     * 合并对象数组并根据key去重
     * @param string $name
     * @param mixed ...$array
     * @return array
     */
    public static function arrayMergeAndUnique(string $key = "name", ...$array):array
    {
        $newArray = [];
        foreach ($array as $k => $arr) {
            if ($k===0){
                $newArray = array_merge($newArray, $arr);
            }else if(is_array($arr)){
                foreach ($arr as $item){
                    $findIndex = Utils::getArrayFindIndex($newArray,function ($row)use ($key,$item){
                        if ($item[$key] === $row[$key]){
                            return true;
                        }
                        return false;
                    });
                    if($findIndex>-1){
                        $data = [];
                        foreach ($item as $itemK=>$itemV){
                            if ( $itemV !== null){
                                $data[$itemK]=$itemV;
                            }
                        }
                        $newArray[$findIndex] = array_merge($newArray[$findIndex],$data);
                    }else{
                        $newArray[]=$item;
                    }
                }
            }
        }


        return $newArray;

    }

    /**
     * 初始化当前所选的应用/版本数据
     * @param $appKey
     */
    public function getCurrentApps(string $appKey,$configData=""):array
    {
        if (!empty($configData)){
            $config =$configData;
        }else{
            $config = Config::get("apidoc")?Config::get("apidoc"):Config::get("apidoc.");
            $config['apps'] = $this->handleAppsConfig($config['apps']);
        }
        if (!(!empty($config['apps']) && count($config['apps']) > 0)) {
            throw new ErrorException("no config apps", 500);
        }
        if (strpos($appKey, ',') !== false) {
            $keyArr = explode(",", $appKey);
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
    public function handleAppsConfig(array $apps,$isHandlePassword=false):array
    {
        $appsConfig = [];
        foreach ($apps as $app) {
            if (!empty($app['password']) && $isHandlePassword===true) {
                unset($app['password']);
                $app['hasPassword'] = true;
            }
            if (!empty($app['title'])){
                $app['title'] = Utils::getLang($app['title']);
            }
            if (!empty($app['items']) && count($app['items']) > 0) {
                $app['items'] = $this->handleAppsConfig($app['items'],$isHandlePassword);
            }
            if (!empty($app['groups']) && count($app['groups']) > 0){
                $app['groups'] = $this->handleGroupsConfig($app['groups']);
            }
            if (!empty($app['headers']) && count($app['headers']) > 0){
                $app['headers'] = Utils::getArrayLang($app['headers'],"desc");
            }
            if (!empty($app['parameters']) && count($app['parameters']) > 0){
                $app['parameters'] = Utils::getArrayLang($app['parameters'],"desc");
            }
            $appsConfig[] = $app;
        }
        return $appsConfig;
    }

    /**
     * 处理groups配置参数
     * @param array $groups
     * @return array
     */
    public function handleGroupsConfig(array $groups):array
    {
        $groupConfig = [];
        foreach ($groups as $group) {
            if (!empty($group['title'])){
                $group['title'] = Utils::getLang($group['title']);
            }
            if (!empty($group['children']) && count($group['children']) > 0) {
                $group['children'] = $this->handleAppsConfig($group['children']);
            }

            $groupConfig[] = $group;
        }
        return $groupConfig;
    }

    /**
     * 驼峰转下划线
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * 字符串转小写
     *
     * @param  string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 创建随机key
     * @param string $prefix
     * @return string
     */
    public static function createRandKey(string $prefix=""): string{
       return uniqid($prefix);
    }

    /**
     * 获取多语言变量值
     * @param $string
     * @return mixed
     */
    public static function getLang($string) {
        if (!$string){
            return $string;
        }
        if (strpos($string, 'lang(') !== false) {
            if (preg_match('#lang\((.*)\)#s', $string, $key) !== false){
                $langKey = $key && count($key)>1 ? trim($key[1]):"";
                return Lang::get($langKey);
            }
        }
        return $string;
    }

    /**
     * 二维数组设置指定字段的多语言
     * @param $array
     * @param $field
     * @return array
     */
    public static function getArrayLang($array,$field){
        $data = [];
        if (!empty($array) && is_array($array)){
            foreach ($array as $item){
                $item[$field] = Utils::getLang($item[$field]);
                $data[]=$item;
            }
        }
        return $data;
    }

    /**
     * 二维数组根据key排序
     * @param $array
     * @param string $field
     * @param int $order
     * @return mixed
     */
    public static function arraySortByKey($array, $field="sort",$order=SORT_ASC){
        $sorts = [];
        foreach ($array as $key => $row) {
            $sorts[$key]  = $row[$field];
        }
        array_multisort($sorts, $order,  $array);
        return $array;
    }





}