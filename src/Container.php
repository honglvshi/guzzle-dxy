<?php

/**
 * 容器类
 */

namespace GuzzleDxy;

use GuzzleDxy\Contracts\CacheInterface;
use GuzzleDxy\Contracts\LoggerInterface;
use GuzzleDxy\Contracts\MonitInterface;
use GuzzleDxy\Events\HttpLockEvent;
use GuzzleDxy\Exceptions\LockException;
use GuzzleDxy\Listeners\HttpSubscriber;
use GuzzleDxy\Tools\UrlTools;
use Symfony\Contracts\EventDispatcher\Event;

class Container
{

    public static $isSetLogger = false;

    public static $isSetCache = false;

    public static $isSetMonit = false;

    private static $monint;

    private static $logger;

    private static $cache;

    private static $ruleContainer = [];

    public static function enableEvent()
    {
        //初始化dispatcher
        Events::initDispatcher();
        //增加订阅者
        Events::addSubscriber(new HttpSubscriber());

        Events::removeListener(HttpLockEvent::class, "httpLock");
    }

    public static function getLogger() : LoggerInterface
    {
        return self::$logger;
    }

    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;

        self::$isSetLogger = true;
    }

    public static function setCache(CacheInterface $cache)
    {
        self::$cache = $cache;

        self::$isSetCache = true;
    }

    public static function getCache() : CacheInterface
    {
        return self::$cache;
    }

    public static function setMoint(MonitInterface $monit)
    {
        self::$monint = $monit;

        self::$isSetMonit = true;
    }

    public static function getMonit() : MonitInterface
    {
        return self::$monint;
    }

    public static function registerUrl(UrlRule $urlRule)
    {
        self::$ruleContainer[$urlRule->getUri()] = $urlRule;
    }

    public static function isRegisterUrl(string $url): bool
    {
        return array_key_exists(UrlTools::getUrlPath($url), self::$ruleContainer);
    }

    public static function getUrlRule(string $url) : UrlRule
    {
        return self::isRegisterUrl($url) ? self::$ruleContainer[UrlTools::getUrlPath($url)] : null;
    }
}