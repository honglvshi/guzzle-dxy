
### 初始化 
#### 依赖
- redis驱动
- 日志驱动 实现 GuzzleDxy\Contracts\LoggerInterface.php
- 预警驱动 实现 GuzzleDxy\Contracts\MonitInterface.php
- url规则 需要集成 UrlRule.php
```
$redisHandler = new Redis();
$redisHandler->connect('127.0.0.1');

\GuzzleDxy\Container::setProjectName("deubg");
\GuzzleDxy\Container::setRedisHandler($redisHandler);
\GuzzleDxy\Container::setNoticeHandler(new WxNotice());
\GuzzleDxy\Container::setLogHandler(new Loghandler());
//\GuzzleDxy\Container::register(new Url502());
```


### http 服务的client 改造
```
$client = new \GuzzleDxy\Http();

$ret = $client->post("*****",[
    "json" => [
        "name" => 'hls',
        "age" => 20,
    ]
]);

var_dump($ret);
exit;
```

###UrlRule介绍

```
<?php

namespace GuzzleDxy;
class UrlRule
{
    //对应的url 全路径
    protected $uri = '';

    //超时限制 超过该值代表 错误请求
    protected $timeoutLimit = 10;

    //规定时间的错误次数限制
    protected $errorLimit = 2;

    //错误时间间隔 60s
    protected $errorInterval = 60;

    //锁住接口时间 
    protected $lockTime = 60;

    //如果报错的模拟返回
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
```
