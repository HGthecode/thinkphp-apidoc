<?php


namespace hg\apidoc;


use think\facade\Cache;

class Auth
{
    public function getToken(){
        $Authorization = md5(uniqid() . time());
        Cache::set($Authorization,time(),3600);
        return $Authorization;
    }
}