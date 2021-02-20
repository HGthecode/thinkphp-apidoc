<?php

namespace hg\apidoc\annotation\explain;

use Doctrine\Common\Annotations\Annotation;

/**
 * 说明
 *
 * @package hg\apidoc\annotation\explain
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class Param extends ParamBase
{


    /**
     * 必须
     * @var bool
     */
    public $require = false;
    
    /**
     * 引入
     * @var string
     */
    public $ref;
}
