<?php

namespace GuzzleDxy\Tools;

class UrlTools
{
    /**
     * 去掉参数判断
     * @param string $url
     * @return string
     */
    public static function getUrlPath(string $url)
    {
        $ret = parse_url($url);

        return $ret['scheme'] . '://' . $ret['host'] . $ret['path'];
    }
}