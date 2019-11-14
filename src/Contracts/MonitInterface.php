<?php

namespace GuzzleDxy\Contracts;

use GuzzleDxy\UrlRule;
use GuzzleHttp\Exception\RequestException;

interface MonitInterface
{
    public function requestExceptionReport(RequestException $requestException);

    public function curlErrorReport(UrlRule $urlRule);

    //接口锁住的通知
    public function lockReport(UrlRule $urlRule);
}
