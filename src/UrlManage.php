<?php

/**
 * url 管理类
 */

namespace GuzzleDxy;

use GuzzleDxy\Tools\UrlTools;

class UrlManage
{

    /** @var bool 是否注册 true 注册 false 没注册 */
    private $isRegister = false;

    private $urlRule;

    /**
     * UrlManage constructor.
     * @param UrlRule $urlRule
     */
    public function __construct(UrlRule $urlRule)
    {
        $this->urlRule = $urlRule;
    }

    private function getUrlRule(): UrlRule
    {
        return $this->urlRule;
    }

    /**
     * 判断url是否被熔断了
     * @return bool
     */
    public function isLock(): bool
    {
        //没有注册的url不再提醒
        if (!$this->isRegister) {
            return false;
        }

        //没有注册缓存驱动
        if (!Container::$isSetCache) {
            return false;
        }

        return empty(Cache::getUrlIsLock($this->urlRule->getUri())) ? false : true;
    }

    /**
     * 超时处理
     */
    public function overTimeoutHandler()
    {
        //没设置缓存驱动 不处理
        if (!Container::$isSetCache) {
            return true;
        }

        $url = $this->urlRule->getUri();
        $errorCount = Cache::getUrlTimeoutCount($url);

        if (empty($errorCount)) {
            Cache::setUrlTimeoutCount(
                $url,
                1,
                $this->urlRule->getTimeoutInterval()
            );
        } else {
            Cache::incrUrlTimeoutCount($url);
        }

        if (++$errorCount < $this->urlRule->getTimeoutErrorLimit()) {

            return true;
        }

        if ($this->urlRule->isNeedLock()) {
            Cache::setUrlIsLock($url, $this->urlRule->getLockTime());
        }

        //是否需要通知
        if (!Cache::getResponseTimeoutIsNotice($url) && Container::$isSetMonit) {
            Container::getMonit()->lockReport($this->urlRule);
            //10秒不再重复推送
            Cache::setResponseTimeoutIsNotice($url, 10);
        }

        return true;
    }

    /**
     * 请求失败处理
     */
    public function responseErrorHandle()
    {
        //没设置缓存驱动 不处理
        if (!Container::$isSetCache) {
            return true;
        }

        $url = $this->urlRule->getUri();

        $errorCount = Cache::getUrlResponseErrorCount($url);

        if (empty($errorCount)) {
            Cache::setUrlResponseErrorCount(
                $url,
                1,
                $this->urlRule->getErrorInterval()
            );
        } else {
            Cache::incrUrlResponseErrorCount($url);
        }

        if (++$errorCount < $this->urlRule->getErrorLimit()) {
            return true;
        }

        if (!Cache::getResponseErrorIsNotice($url) && Container::$isSetMonit) {
            Container::getMonit()->curlErrorReport($this->urlRule);
            //10秒不再重复推送
            Cache::setResponseErrorIsNotice($url, 10);
        }

        return true;

    }


}