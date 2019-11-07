<?php

namespace GuzzleDxy;


use GuzzleDxy\Contracts\LoggerInterface;
use GuzzleDxy\Contracts\MonitInterface;

class Container
{

    private static $ruleArray = [];

    private static $logHandler =  null;
    private static $noticeHandler = null;
    public static $isRegisterNotice = false;
    public static $isRegisterLog = false;
    private static $redisHandler;

    private static $projectName = 'php-';


    public static function getLogHandler() : LoggerInterface
    {
        return self::$logHandler;
    }

    public static function setLogHandler(LoggerInterface $logger)
    {
        self::$logHandler = $logger;
        self::$isRegisterLog = true;
    }

    public static function setNoticeHandler(MonitInterface $monit)
    {
        self::$noticeHandler = $monit;
        self::$isRegisterNotice = true;
    }

    public static function getNoticeHandler() : MonitInterface
    {
        return self::$noticeHandler;
    }

    public static function setProjectName(string $projectName)
    {
        self::$projectName = $projectName;
    }

    public static function getProjectName()
    {
        return self::$projectName;
    }

    public static function setRedisHandler(\Redis $redis)
    {
        self::$redisHandler = new HttpCache($redis);
    }

    public static function getRedisHandler(): HttpCache
    {
        if (empty(self::$redisHandler)) {
            return null;
        }

        return self::$redisHandler;
    }

    public static function register(UrlRule $class): void
    {
        self::$ruleArray[$class->getUri()] = $class;
    }

    public static function hasRuleByUrl(string $url): bool
    {
        return array_key_exists($url, self::$ruleArray);
    }

    public static function getRuleByUrl(string $url): UrlRule
    {
        return self::$ruleArray[$url];
    }

}