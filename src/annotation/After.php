<?php

namespace hg\apidoc\annotation;


/**
 * 接口调试前置事件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD"})
 */
final class After
{

    /**
     * key
     * @var string
     */
    public $key;

    /**
     * 事件
     * @Enum({"setGlobalHeader", "setGlobalParam", "clearGlobalHeader", "clearGlobalParam"})
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
