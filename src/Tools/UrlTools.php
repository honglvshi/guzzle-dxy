<?php

namespace GuzzleDxy\Tools;

class UrlTools
{
    public static function getUrlPath(string $url)
    {
        $ret = parse_url($url);

        return $ret['scheme'] . '://' . $ret['host'] . $ret['path'];
    }
}