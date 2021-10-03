<?php

namespace hg\apidoc\annotation;


use Doctrine\Common\Annotations\Annotation;

/**
 * 接口调试前置事件
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","ANNOTATION"})
 */
final class Before extends Annotation
{

    /**
     * key
     * @var string
     */
    public $key;

    /**
     * 事件
     * @Enum({"setHeader", "setGlobalHeader", "setParam", "setGlobalParam", "clearGlobalHeader", "clearGlobalParam", "clearParam","handleParam",""})
     * @var string
     */
    public $event;

    /**
     * ajax时的url
     * @var string
     */
    public $url;

    /**
     * ajax时的Method
     * @Enum({"GET", "POST", "PUT", "DELETE"})
     * @var string
     */
    public $method;

    /**
     * ajax时的 content-type
     * @var string
     */
    public $contentType;

    /**
     * 描述
     * @var string
     */
    public $desc;

    /**
     * 引用
     * @var string
     */
    public $ref;

}
