<?php


namespace hg\apidoc\exception;


use hg\apidoc\Utils;
use think\Exception;
use think\exception\HttpException;

class ErrorException extends HttpException
{

    protected $exceptions = [
        'appkey not found'     => ['code' => 4005, 'msg' => '缺少必要参数appKey'],
        'mdPath not found'     => ['code' => 4006, 'msg' => '缺少必要参数path'],
        'appKey error'         => ['code' => 4007, 'msg' => '不存在 folder为${appKey}的apps配置'],
        'template not found'   => ['code' => 4008, 'msg' => '${template}模板不存在'],
        'path not found'       => ['code' => 4009, 'msg' => '${path}目录不存在'],
        'classname error'      => ['code' => 4010, 'msg' => '${classname}文件名不合法'],
        'no config apps'       => ['code' => 5000, 'msg' => 'apps配置不可为空'],
        'no debug'             => ['code' => 5001, 'msg' => '请在debug模式下，使用该功能'],
        'no config crud'       => ['code' => 5002, 'msg' => 'crud未配置'],
        'datatable crud error' => ['code' => 5003, 'msg' => '数据表创建失败，请检查配置'],
        'file already exists' => ['code' => 5004, 'msg' => '${filepath}文件已存在'],

    ];

    public function __construct(string $exceptionCode, int $statusCode = 412, array $data = [])
    {
        $exception = $this->getException($exceptionCode);
        $msg       = Utils::replaceTemplate($exception['msg'], $data);
        parent::__construct($statusCode, $msg, null, [], $exception['code']);
    }

    public function getException($exceptionCode)
    {
        if (isset($this->exceptions[$exceptionCode])) {
            return $this->exceptions[$exceptionCode];
        }
        throw new Exception('exceptionCode "' . $exceptionCode . '" Not Found');
    }

}