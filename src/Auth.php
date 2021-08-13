<?php
declare(strict_types = 1);

namespace hg\apidoc;

use think\facade\Config;
use think\facade\Request;
use hg\apidoc\exception\AuthException;

class Auth
{
    protected $config = [];


    public function __construct()
    {
        $this->config = Config::get('apidoc')?Config::get('apidoc'):Config::get('apidoc.');
    }

    /**
     * 验证密码
     * @param $password
     * @return false|string
     */
    public function verifyAuth(string $password, string $appKey)
    {
        if (!empty($appKey)) {
            $currentApps = (new Utils())->getCurrentApps($appKey);
            $currentApp  = $currentApps[count($currentApps) - 1];
            if (!empty($currentApp) && !empty($currentApp['password'])) {
                // 应用密码
                if (md5($currentApp['password']) === $password) {
                    return $this->createToken($currentApp['password']);
                }
                throw new AuthException("password error");
            }
        }
        if ($this->config['auth']['enable']) {
            // 密码验证
            if (md5($this->config['auth']['password']) === $password) {
                return $this->createToken($this->config['auth']['password']);
            }
            throw new AuthException("password error");
        }
        return false;
    }

    /**
     * 获取tokencode
     * @param string $password
     * @return string
     */
    protected function getTokenCode(string $password): string
    {
//        return md5(md5($password) . strtotime(date('Y-m-d', time())));
        return md5(md5($password));
    }


    /**
     * 创建token
     * @param string $password
     * @return string
     */
    public function createToken(string $password): string
    {
        $expire = $this->config['auth']['expire']?$this->config['auth']['expire']:86400;
        $data = [
            'key'=>$this->getTokenCode($password),
            'expire'=>time()+$expire
        ];
        $code = json_encode($data);
        return $this->handleToken($code, "CE");
    }

    /**
     * 验证token
     * @param $token
     * @return bool
     */
    public function checkToken(string $token, string $password): bool
    {
        if (empty($password)) {
            $password = $this->config['auth']['password'];
        }
        $decode = $this->handleToken($token, "DE");
        $deData = json_decode($decode,true);

        if (!empty($deData['key']) && $deData['key'] === $this->getTokenCode($password) && !empty($deData['expire']) && $deData['expire']>time()){
            return true;
        }


//        if ($decode === $this->getTokenCode($password)) {
//            return true;
//        }
        return false;
    }

    /**
     * @param $request
     * @return bool
     */
    public function checkAuth(string $appKey): bool
    {
        $config  = $this->config;
        $request = Request::instance();

        $token = $request->param("token");

        if (!empty($appKey)) {
            $currentApps = (new Utils())->getCurrentApps($appKey);
            $currentApp  = $currentApps[count($currentApps) - 1];
            if (!empty($currentApp) && !empty($currentApp['password'])) {
                if (empty($token)) {
                    throw new AuthException("token not found");
                }
                // 应用密码
                if ($this->checkToken($token, $currentApp['password'])) {
                    return true;
                } else {
                    throw new AuthException("token error");
                }
            } else if (!(!empty($config['auth']) && $config['auth']['enable'])) {
                return true;
            }
        }
        if(!empty($config['auth']) && $config['auth']['enable'] && empty($token)){
            throw new AuthException("token not found");
        }else if (!empty($token) && !$this->checkToken($token, "")) {
            throw new AuthException("token error");
        }
        return true;
    }

    /**
     * 处理token
     * @param $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return false|string
     */
    protected function handleToken(string $string, string $operation = 'DE', string $key = '', int $expiry = 0):string
    {
        $ckey_length   = 4;
        $key           = md5($key ? $key : $this->config['auth']['secret_key']);
        $keya          = md5(substr($key, 0, 16));
        $keyb          = md5(substr($key, 16, 16));
        $keyc          = $ckey_length ? ($operation == 'DE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey      = $keya . md5($keya . $keyc);
        $key_length    = strlen($cryptkey);
        $string        = $operation == 'DE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result        = '';
        $box           = range(0, 255);
        $rndkey        = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result  .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

}