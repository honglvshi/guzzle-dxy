<?php

namespace GuzzleDxy;

use GuzzleDxy\Tools\UrlTools;

class HttpManager
{
    public function isRequest(string $url): bool
    {
        $url = UrlTools::getUrlPath($url);

        $isRegister = Container::hasRuleByUrl($url);

        if (!$isRegister) {
            return true;
        }

        $isLock = Container::getRedisHandler()->getUrlIsLock($url);

        return ($isLock === true) ? false : true;
    }

    public function returnMockResponse(string $url)
    {
        $url = UrlTools::getUrlPath($url);

        $isRegister = Container::hasRuleByUrl($url);

        if (!$isRegister) {
            //没有注册url的默认
            return ["code" => 99999, "exception" => '系统异常'];
        }

        return Container::getRuleByUrl($url)->getResponseMock();
    }

    public function httpErrorHandler($url)
    {
        if (Container::hasRuleByUrl($url) === false) {
            return true;
        }

        $urlRule = Container::getRuleByUrl($url);

        $info = Container::getRedisHandler()->getUrlInfoCache($url);

        Container::getRedisHandler()->incErrorCount($url);


        if ($info['error_count'] >= $urlRule->getErrorLimit()) {
            //判断是否已经通知
            if ($info['is_report'] !== 1) {
                Container::getNoticeHandler()->curlErrorReport($url, $urlRule);

                Container::getRedisHandler()->setIsReport($url);
            }

            //锁住url
            Container::getRedisHandler()->setUrlIsLock($url);
        }

        return true;

    }

}

