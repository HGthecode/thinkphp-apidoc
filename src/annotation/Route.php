<?php

namespace hg\apidoc\annotation;

use Doctrine\Common\Annotations\Annotation\Enum;


final class Route extends Rule
{
    /**
     * 请求类型
     * @Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"})
     * @var string
     */
    public $method = "GET";

}
