<?php

namespace GuzzleDxy\Exceptions;

use Throwable;

class LockException extends \Exception
{
    private $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function __construct($url)
    {
        parent::__construct("{$url}接口被锁定,目前无法反问", 9990);
    }
}
