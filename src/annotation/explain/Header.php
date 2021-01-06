<?php

namespace hg\apidoc\annotation\explain;

use Doctrine\Common\Annotations\Annotation;

/**
 * 请求头
 *
 * @package hg\apidoc\annotation\explain
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Header extends Annotation
{
    /**
     * 必须
     * @var bool
     */
    public $require = false;


    /**
     * 描述
     * @var string
     */
    public $desc;


}
