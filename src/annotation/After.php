<?php

namespace hg\apidoc\annotation;


use Doctrine\Common\Annotations\Annotation;

/**
 * 接口调试前置事件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","ANNOTATION"})
 */
final class After extends EventBase
{



    /**
     * 事件
     * @Enum({"setGlobalHeader", "setGlobalParam", "clearGlobalHeader", "clearGlobalParam","ajax"})
     * @var string
     */
    public $event;




}
