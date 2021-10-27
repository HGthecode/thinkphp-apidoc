<?php

namespace hg\apidoc\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * md返回参数
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","ANNOTATION"})
 */
final class ReturnedMd extends Annotation
{
    /**
     * 引入
     * @var string
     */
    public $ref;

}
