
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