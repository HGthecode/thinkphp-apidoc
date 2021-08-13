<?php
declare(strict_types = 1);

namespace hg\apidoc\parseApi;


use hg\apidoc\Utils;
use think\facade\Cache;
use think\facade\Config;

class CacheApiData
{
    protected $config = [];

    public function __construct()
    {
        $this->config = Config::get('apidoc');
    }


    /**
     * 获取接口缓存数据
     * @param string $appKey
     * @param string $cacheFileName
     * @return array|false
     */
    public function get(string $appKey)
    {
        $json = Cache::get("apidoc_".$appKey);
        return $json;

    }

    /**
     * 设置接口缓存
     * @param string $appKey
     * @param array $json
     * @return array|false
     */
    public function set(string $appKey, array $json)
    {
        if (empty($json)) {
            return false;
        }
        Cache::tag("apidoc")->set("apidoc_".$appKey,$json);


    }
}