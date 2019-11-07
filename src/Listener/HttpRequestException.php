<?php

namespace GuzzleDxy\Listener;

use GuzzleDxy\Container;
use GuzzleDxy\HttpManager;
use GuzzleHttp\Exception\RequestException;

class HttpRequestException
{
    public static function handler(RequestException $exception)
    {
        if (Container::$isRegisterLog) {
            Container::getLogHandler()->error($exception);
        }

        if (!Container::$isRegisterNotice) {
            return true;
        }

        //加锁 10s 报错一次 不然会出现雪崩
        if (Container::getRedisHandler()->getIsExceptionReport() != 1) {
            Container::getNoticeHandler()->requestExceptionReport($exception);
            Container::getRedisHandler()->setIsException();
        }

        $manage = new HttpManager();

        $manage->httpErrorHandler($exception->getRequest()->getUri());

    }
}