<?php

namespace GuzzleDxy\Contracts;

use GuzzleDxy\Result;
use GuzzleHttp\Exception\RequestException;

interface LoggerInterface
{
    public function info(Result $result);
    public function error(RequestException $exception);
}