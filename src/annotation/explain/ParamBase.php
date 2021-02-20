<?php

namespace hg\apidoc\annotation\explain;

use Doctrine\Common\Annotations\Annotation;

abstract class ParamBase extends Annotation
{


    

    /**
     * 类型
     * @Enum({"string", "integer", "boolean", "array", "double","object"})
     * @var string
     */
    public $type = 'string';


    /**
     * 默认值
     * @var string
     */
    public $default;

    /**
     * 描述
     * @var string
     */
    public $desc;


}
