<?php

namespace GuzzleDxy;
class HttpCache
{
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function getUrlIsLock($url)
    {
        $key = HttpCacheKey::getUrlLockKey(Container::getProjectName(), $url);

        return ($this->redis->get($key) == 1) ? true : false;
    }

    public function setUrlIsLock($url)
    {
        $key = HttpCacheKey::getUrlLockKey(Container::getProjectName(), $url);

        $urlRule = Container::getRuleByUrl($url);

        return $this->redis->set($key, 1, $urlRule->getLockTime());
    }

    public function incErrorCount($url)
    {
        $key = HttpCacheKey::getUrlInfoKey(Container::getProjectName(), $url);
        $exist = $this->redis->exists($key);

        $isFirstInit = ($exist == 0) ? true : false;

        $urlRule = Container::getRuleByUrl($url);

        $this->redis->hIncrBy($key, "error_count", 1);

        if ($isFirstInit) {
            $this->redis->expire($key, $urlRule->getErrorInterval());
        }

    }

    public function setIsReport($url)
    {
        $key = HttpCacheKey::getUrlInfoKey(Container::getProjectName(), $url);

        return $this->redis->hSet($key, "is_report", 1);
    }

    public function getUrlInfoCache($url)
    {
        $cache = $this->redis->hGetAll(HttpCacheKey::getUrlInfoKey(Container::getProjectName(), $url));

        return [
            "error_count" => array_key_exists("error_count", $cache) ? $cache['error_count'] : 0,
            "is_report" => array_key_exists("is_report", $cache) ? $cache['is_report'] : 0,
        ];
    }

    public function getIsExceptionReport()
    {
        return $this->redis->get(HttpCacheKey::isExceptionReportKey(Container::getProjectName()));
    }

    public function setIsException()
    {
        $this->redis->set(
            HttpCacheKey::isExceptionReportKey(Container::getProjectName()),
            1,
            10
        );
    }
}
