<?php

namespace hg\apidoc\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * md请求参数
 * @package hg\apidoc\annotation
 * @Annotation
 * @Target({"METHOD","ANNOTATION"})
 */
final class ParamMd extends Annotation
{
    /**
     * 引入
     * @var string
     */
    public $ref;

}
