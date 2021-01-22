<?php
namespace hg\apidoc;

use think\App;
use think\facade\Request;

class Auth
{
    protected $config=[];

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 验证密码
     * @param $password
     * @return false|string
     */
    public function verifyAuth($password){
        if ($this->config['auth']['enable']){
            // 密码验证
            if (md5($this->config['auth']['password']) === $password){
                $token = md5(md5($this->config['auth']['password']).$this->config['auth']['secret_key'].strtotime(date('Y-m-d',time())));
                return $token;
            }
            throw new \think\Exception("密码不正确，请重新输入");
        }
        return false;
    }

    /**
     * 验证token
     * @param $token
     * @return bool
     */
    public function checkToken($token){
        if ($token === md5(md5($this->config['auth']['password']).$this->config['auth']['secret_key'].strtotime(date('Y-m-d',time())))){
            return true;
        }
        return false;
    }
}