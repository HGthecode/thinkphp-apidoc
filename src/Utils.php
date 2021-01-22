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
}