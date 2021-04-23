<?php


namespace hg\apidoc\exception;


use think\Exception;
use think\exception\HttpException;

class AuthException extends HttpException
{

    protected $exceptions = [
        'password error'     => ['code' => 4001, 'msg' => '密码不正确，请重新输入'],
        'password not found' => ['code' => 4002, 'msg' => '密码不可为空'],
        'token error'        => ['code' => 4003, 'msg' => '不合法的Token'],
        'token not found'    => ['code' => 4004, 'msg' => '不存在Token'],
    ];

    public function __construct(string $exceptionCode)
    {
        $exception = $this->getException($exceptionCode);
        parent::__construct(401, $exception['msg'], null, [], $exception['code']);
    }

    public function getException($exceptionCode)
    {
        if (isset($this->exceptions[$exceptionCode])) {
            return $this->exceptions[$exceptionCode];
        }
        throw new Exception('exceptionCode "' . $exceptionCode . '" Not Found');
    }
}