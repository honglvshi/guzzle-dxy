<?php

namespace GuzzleDxy;

use GuzzleDxy\Listener\AfterHttpRequest;
use GuzzleDxy\Listener\HttpRequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Http
{
    private $client;

    private $response;

    private $result;

    public function __construct(array $config = [])
    {
        $config['timeout'] = 5;
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());

        $this->result = new Result();

        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            $this->result->setRequest($request);
            $this->result->setStartTime(microtime(true));
            return $request;
        }));

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $this->result->setResponse($response);
            $this->result->setEndTime(microtime(true));

            //写入日志
            if (Container::$isRegisterLog) {
                Container::getLogHandler()->info($this->result);
            }

            return $response;
        }));

        $config['handler'] = $stack;
        $this->client = new Client($config);
    }

    public function get(string $url, array $header = [])
    {
        return $this->server(
            $url,
            "GET",
            $header
        );
    }

    public function post(string $url, array $header = [])
    {
        return $this->server(
            $url,
            "POST",
            $header
        );
    }

    public function put(string $url, array $header = [])
    {
        return $this->server(
            $url,
            "PUT",
            $header
        );
    }

    public function server(string $url, string $method, array $header = [])
    {
        //判断url能发送
        $manage = new HttpManager();

        $isRequest = $manage->isRequest($url);

        if (!$isRequest) {
            //@todo 抛异常 还是 返回mock数据 需要商量
            return $manage->returnMockResponse($url);
        }

        $startTime = microtime(true);

        try {
            $response = $this->client->request(
                $method,
                $url,
                $header
            );

            $excuteTime = round(microtime(true) - $startTime, 2);

            AfterHttpRequest::handler(
                $url,
                $response,
                $excuteTime
            );

        } catch (RequestException $requestException) {
            HttpRequestException::handler($requestException);
            return $manage->returnMockResponse($url);

        } catch (GuzzleException $guzzleException) {
            //@todo guzzle的异常是否需要捕获 直接异常让sentry接管（洪吕石）
            \GuzzleDxy\Listener\GuzzleException::handler($guzzleException);
            return $manage->returnMockResponse($url);
        }

        //@todo 如果第三方出错了 是否需要屏蔽错误
        if ($response->getStatusCode() >= 400) {
            return $manage->returnMockResponse($url);
        }

        $this->response = $response;
        return (string)$response->getBody();
    }

}
