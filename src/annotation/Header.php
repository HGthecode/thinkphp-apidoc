<?php

namespace hg\apidoc\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * 请求头
 *
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD"})
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
