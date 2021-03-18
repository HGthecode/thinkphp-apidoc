<?php


namespace hg\apidoc;


class Utils
{
    /**
     * 过滤参数字段
     * @param $data
     * @param $fields
     * @param string $type
     * @return array
     */
    public function filterParamsField($data,$fields,$type="field"){
        $fieldArr=explode(',' , $fields);
        $dataList=[];
        foreach ($data as $item){
            if (!empty($item['name']) && in_array($item['name'], $fieldArr) && $type==='field'){
                $dataList[]=$item;
            }else if (!(!empty($item['name']) && in_array($item['name'], $fieldArr)) && $type =="withoutField"){
                $dataList[]=$item;
            }
        }
        return $dataList;
    }

    /**
     * 读取文件内容
     * @param $fileName
     * @return false|string
     */
    public static function getFileContent($fileName)
    {
        $content="";
        if (file_exists($fileName)){
            $handle = fopen($fileName, "r");
            $content = fread($handle, filesize ($fileName));
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
    public static function createFile($path,$str_tmp){
        $pathArr = explode("/", $path);
        unset($pathArr[count($pathArr)-1]);
        $dir = implode("/", $pathArr);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $fp=fopen($path,"w") or die("Unable to open file!");
        fwrite($fp,$str_tmp); //存入内容
        fclose($fp);
        return true;
    }

    /**
     * 删除文件
     * @param $path
     */
    public static function delFile($path){
        $url=iconv('utf-8','gbk',$path);
        if(PATH_SEPARATOR == ':'){ //linux
            unlink($path);
        }else{  //Windows
            unlink($url);
        }
    }

    /**
     * 将tree树形数据转成list数据
     * @param  array  $tree        tree数据
     * @param  string $childName  子节点名称
     * @return array  转换后的list数据
     */
    public function treeToList($tree,  $childName = 'children',$key="id",$parentField="parent")
    {
        $array = array();
        foreach ($tree as $val) {
            $array[] = $val;
            if (isset($val[$childName])) {
                $children = $this->treeToList($val[$childName], $childName);
                if ($children) {
                    $newChildren = [];
                    foreach ($children as $item){
                        $item[$parentField] = $val[$key];
                        $newChildren[]=$item;
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
    public function getTreeNodesByKeys($tree,$keys,$field="id",$childrenField="children"){
        $list = $this->TreeToList($tree,$childrenField,"folder");
        $data = [];
        foreach ($keys as $k=>$v){
            $parent = !$k?"":$keys[$k-1];
            foreach ($list as $item){
                if (((!empty($item['parent']) && $item['parent'] === $parent) || empty($item['parent'])) && $item[$field] == $v){
                    $data[]=$item;
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
    public static function replaceTemplate($temp,$data){
        $str = $temp;
        foreach ($data as $k=>$v){
            $key='${'.$k.'}';
            if (strpos($str,$key)!==false){
                $str=str_replace($key,$v,$str);
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
    public function replaceCurrentAppTemplate($temp,$currentApps){
        $str = $temp;
        if (!empty($currentApps) && count($currentApps)>0){
            $data = [];
            for ($i=0; $i<=3; $i++) {
                if (isset($currentApps[$i])){
                    $appItem =$currentApps[$i];
                    foreach ($appItem as $k=>$v){
                        $key = 'app['.$i.'].'.$k;
                        $data[$key]=$v;
                    }
                }else{
                    $appItem=$currentApps[0];
                    foreach ($appItem as $k=>$v){
                        $key = 'app['.$i.'].'.$k;
                        $data[$key]="";
                    }
                }
            }
            $str = $this->replaceTemplate($str,$data);
        }
        return $str;
    }

    public static function getArrayFind($array,$query){
        $res=null;
        if (is_array($array)){
            foreach ($array as $item){
                if ($query($item)){
                    $res= $item;
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
    public static function arrayMergeAndUnique($key="name",...$array){
        $mergeArr=[];
        foreach ($array as $k => $v) {
            $mergeArr=array_merge($mergeArr,$v);
        }
        $keys = [];
        foreach ($mergeArr as $k => $v) {
            $keys[]=$v[$key];
        }
        $uniqueKeys=array_flip(array_flip($keys));
        $newArray=[];
        foreach ($uniqueKeys as $k=>$v){
            $newArray[]=$mergeArr[$k];
        }
        return $newArray;

    }



}