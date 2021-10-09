<?php

namespace hg\apidoc\annotation;


use Doctrine\Common\Annotations\Annotation;

/**
 * 接口调试前置事件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","ANNOTATION"})
 */
final class Before extends EventBase
{
    /**
     * 事件
     * @Enum({"setHeader", "setGlobalHeader", "setParam", "setGlobalParam", "clearGlobalHeader", "clearGlobalParam", "clearParam","handleParam",""})
     * @var string
     */
    public $event;

}
