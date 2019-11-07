<?php

namespace GuzzleDxy;
class UrlRule
{
    protected $uri = '';

    protected $timeoutLimit = 10;

    protected $errorLimit = 2;

    protected $errorInterval = 60;

    protected $lockTime = 60;

    protected $responseMock = [];

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return int
     */
    public function getTimeoutLimit()
    {
        return $this->timeoutLimit;
    }

    /**
     * @param int $timeoutLimit
     */
    public function setTimeoutLimit($timeoutLimit)
    {
        $this->timeoutLimit = $timeoutLimit;
    }

    /**
     * @return int
     */
    public function getErrorLimit()
    {
        return $this->errorLimit;
    }

    /**
     * @param int $errorLimit
     */
    public function setErrorLimit($errorLimit)
    {
        $this->errorLimit = $errorLimit;
    }

    /**
     * @return int
     */
    public function getErrorInterval()
    {
        return $this->errorInterval;
    }

    /**
     * @param int $errorInterval
     */
    public function setErrorInterval($errorInterval)
    {
        $this->errorInterval = $errorInterval;
    }

    /**
     * @return int
     */
    public function getLockTime()
    {
        return $this->lockTime;
    }

    /**
     * @param int $lockTime
     */
    public function setLockTime($lockTime)
    {
        $this->lockTime = $lockTime;
    }

    /**
     * @return array
     */
    public function getResponseMock()
    {
        return $this->responseMock;
    }

    /**
     * @param array $responseMock
     */
    public function setResponseMock($responseMock)
    {
        $this->responseMock = $responseMock;
    }
}