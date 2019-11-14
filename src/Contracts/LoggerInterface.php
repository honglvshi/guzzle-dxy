<?php

namespace GuzzleDxy\Contracts;

use GuzzleDxy\Result;
use GuzzleHttp\Exception\RequestException;

interface LoggerInterface
{
    // 请求日志保存接口
    public function info(Result $result);

    // 请求发生异常的日志接口
    public function error(RequestException $exception);
}
