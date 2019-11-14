<?php

namespace GuzzleDxy;

use GuzzleDxy\Events\HttpExceptionEvent;
use GuzzleDxy\Events\HttpLockEvent;
use GuzzleDxy\Events\HttpResponseEvent;
use GuzzleDxy\Exceptions\HttpException;
use GuzzleDxy\Exceptions\LockException;
use GuzzleHttp\Client;
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
            Events::dispatch(new HttpResponseEvent($this->result));
            $this->response = $response;
            return $response;
        }));

        $config['handler'] = $stack;
        $this->client = new Client($config);
    }

    public function get(string $url, array $header = [])
    {
        return $this->server($url, "GET", $header);
    }

    public function post(string $url, array $header = [])
    {
        return $this->server($url, "POST", $header);
    }

    public function put(string $url, array $header = [])
    {
        return $this->server($url, "PUT", $header);
    }


    /**
     * @param string $url
     * @param string $method
     * @param array $header
     * @return string
     * @throws LockException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function server(string $url, string $method, array $header = [])
    {
        if (Container::isRegisterUrl($url) && (new UrlManage(Container::getUrlRule($url)))->isLock()) {
            Events::dispatch(new HttpLockEvent());
            throw new LockException($url);
        }

        try {
            $response = $this->client->request($method, $url, $header);
        } catch (RequestException $requestException) {
            Events::dispatch(new HttpExceptionEvent($requestException));
            throw $requestException;
        }

        return (string)$response->getBody();
    }

    /**
     * 获取上一次请求的response
     * @return ResponseInterface
     */
    public function getPreviousResponse(): ResponseInterface
    {
        return $this->response;
    }


}
