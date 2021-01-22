<?php

namespace hg\apidoc\annotation;

use Doctrine\Common\Annotations\Annotation;

abstract class ParamBase extends Annotation
{

    /**
     * 类型
     * @Enum({"string", "integer", "int", "boolean", "array", "double", "object", "tree", "file"})
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

    /**
     * 为tree类型时指定children字段
     * @var string
     */
    public $childrenField = 'children';

    /**
     * 为tree类型时指定children字段说明
     * @var string
     */
    public $childrenDesc = 'children';

    /**
     * 指定引入的字段
     * @var string
     */
    public $field;

    /**
     * 指定从引入中过滤的字段
     * @var string
     */
    public $withoutField;



}
