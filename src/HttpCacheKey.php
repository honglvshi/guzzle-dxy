<?php

namespace GuzzleDxy;

class HttpCacheKey
{
    public static function getUrlLockKey($projectName, $url)
    {
        return vsprintf(
            "%s-curl-manage-lock-%s",
            [$projectName, $url]
        );
    }

    public static function getUrlInfoKey($projectName, $url)
    {
        return vsprintf(
            "%s-curl-manage-%s",
            [$projectName, $url]
        );
    }

    public static function isExceptionReportKey($projectName)
    {
        return vsprintf(
            "%s-exception-is_report",
            [$projectName]
        );
    }
}