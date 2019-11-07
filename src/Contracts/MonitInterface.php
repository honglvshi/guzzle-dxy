<?php

namespace GuzzleDxy\Contracts;

use GuzzleDxy\Result;
use GuzzleDxy\UrlRule;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

interface MonitInterface
{
    public function requestExceptionReport(RequestException $requestException);

    public function guzzleExceptionReport(GuzzleException $guzzleException);

    public function curlErrorReport($url,UrlRule $urlRule);
}
