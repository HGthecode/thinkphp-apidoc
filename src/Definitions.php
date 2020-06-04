<?php
namespace hg\apidoc;
// 用于设置文档自动生成的配置

// 一些通用的参数配置
class Definitions
{

    /**
     * @title 获取分页数据列表
     * @author HG
     * @param name:pageIndex type:int require:0 default:0 desc:查询页数
     * @param name:pageSize type:int require:0 default:20 desc:查询条数
     */
    public function pagingParam(){}


    /**
     * @title 获取一条数据明细
     * @author HG
     * @param name:id type:int require:1 default: desc:唯一id
     */
    public function getInfo(){}
}