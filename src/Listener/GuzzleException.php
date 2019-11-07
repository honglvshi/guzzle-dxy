<?php

namespace GuzzleDxy\Listener;

use GuzzleDxy\Container;
use GuzzleDxy\HttpManager;
use GuzzleHttp\Exception\RequestException;

class GuzzleException
{
    public static function handler(\GuzzleHttp\Exception\GuzzleException $exception)
    {

        if (!Container::$isRegisterNotice) {
            return true;
        }

        //加锁 10s 报错一次 不然会出现雪崩报错
        if (Container::getRedisHandler()->getIsExceptionReport() != 1) {
            Container::getNoticeHandler()->guzzleExceptionReport($exception);
            Container::getRedisHandler()->setIsException();
        }

    }
}