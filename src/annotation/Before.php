<?php

namespace hg\apidoc\annotation;


/**
 * 接口调试前置事件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD"})
 */
final class Before
{

    /**
     * key
     * @var string
     */
    public $key;

    /**
     * 事件
     * @Enum({"setHeader", "setGlobalHeader", "setParam", "setGlobalParam", "clearGlobalHeader", "clearGlobalParam", "clearParam"})
     * @var string
     */
    public $event;

    /**
     * 值
     * @var string
     */
    public $value;

    /**
     * 描述
     * @var string
     */
    public $desc;


}
