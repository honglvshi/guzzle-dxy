<?php

namespace GuzzleDxy\Listener;

use GuzzleDxy\Container;
use GuzzleDxy\HttpManager;
use GuzzleDxy\Tools\UrlTools;
use Psr\Http\Message\ResponseInterface;

class AfterHttpRequest
{
    public static function handler($url, ResponseInterface $response, $excuteTime)
    {

        $url = UrlTools::getUrlPath($url);

        //日志处理
        if (Container::hasRuleByUrl($url) === false) {
            return true;
        }

        $urlRule = Container::getRuleByUrl($url);

        if ($response->getStatusCode() < 400) {
            if ($excuteTime <= $urlRule->getTimeoutLimit() || $urlRule->getTimeoutLimit() == 0) {
                return true;
            }
        }


        $manage = new HttpManager();

        $manage->httpErrorHandler($url);

        return true;
    }
}