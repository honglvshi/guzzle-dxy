<?php

namespace GuzzleDxy\Listeners;

use GuzzleDxy\Cache;
use GuzzleDxy\Container;
use GuzzleDxy\Events\HttpExceptionEvent;
use GuzzleDxy\Events\HttpLockEvent;
use GuzzleDxy\Events\HttpResponseEvent;
use GuzzleDxy\Exceptions\LockException;
use GuzzleDxy\Result;
use GuzzleDxy\Tools\UrlTools;
use GuzzleDxy\UrlManage;
use GuzzleDxy\UrlRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HttpSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            HttpResponseEvent::class => [
                ["httpResponseLog", 999],
                ["httpResponseTimeout", 998],
                ["httpResponseError", 997],
            ],
            HttpExceptionEvent::class => [
                ["httpException", 999]
            ],
            HttpLockEvent::class => [
                "httpLock" => 999
            ]
        ];
    }

    /**
     * 保存日志
     * @param HttpResponseEvent $httpResponse
     */
    public function httpResponseLog(HttpResponseEvent $httpResponse)
    {
        //保存日志
        if (Container::$isSetLogger) {
            Container::getLogger()->info($httpResponse->getResult());
        }
    }

    /**
     * 判断是否超时
     * @param HttpResponseEvent $httpResponse
     */
    public function httpResponseTimeout(HttpResponseEvent $httpResponse)
    {
        $url = $httpResponse->getResult()->getRequest()->getUri();

        $isRegister = Container::isRegisterUrl($url);
        if (!$isRegister) {
            return true;
        }

        $urlRule = Container::getUrlRule($url);

        $isOverTimeout = $this->responseIsOverTimeout($httpResponse->getResult(), $urlRule);

        if (!$isOverTimeout) {
            return true;
        }

        return (new UrlManage($urlRule))->overTimeoutHandler();
    }

    /**
     * 判断是否请求失败
     * @param HttpResponseEvent $httpResponseEvent
     */
    public function httpResponseError(HttpResponseEvent $httpResponseEvent)
    {
        $url = $httpResponseEvent->getResult()->getRequest()->getUri();

        $isRegister = Container::isRegisterUrl($url);
        if (!$isRegister) {
            return true;
        }

        $urlRule = Container::getUrlRule($url);

        if ($this->getResponseIsSuccess($httpResponseEvent->getResult(), $urlRule)) {
            return true;
        }

        return (new UrlManage($urlRule))->responseErrorHandle();

    }


    private function responseIsOverTimeout(Result $result, UrlRule $urlRule)
    {
        $diff = round(($result->getEndTime() - $result->getStartTime()), 2);

        return ($diff >= $urlRule->getTimeoutLimit()) ? true : false;
    }

    /**
     * 判断http响应返回是否成功
     * @param Result $result
     * @param UrlRule $
     */
    private function getResponseIsSuccess(Result $result, UrlRule $urlRule)
    {
        $responseStatusCode = (string)$result->getResponse()->getStatusCode();

        if ($responseStatusCode < 400) {
            return true;
        }

        if (in_array($responseStatusCode, $urlRule->getWhiteResponseCodeList())) {
            return true;
        }

        return false;
    }

    /**
     * @param HttpExceptionEvent $httpException
     */
    public function httpException(HttpExceptionEvent $httpException)
    {

        $url = UrlTools::getUrlPath(
            (string)$httpException->getRequestException()->getRequest()->getUri()
        );

        if (Container::$isSetLogger) {
            Container::getLogger()->error($httpException);
        }

        if (Container::$isSetMonit && !empty(Cache::getResponseExceptionIsNotice($url))) {
            Container::getMonit()->requestExceptionReport($httpException);
            Cache::setResponseExceptionIsNotice($url, 10);
        }

    }

    /**
     * @param HttpLockEvent $httpLockEvent
     * @throws LockException
     */
    public function httpLock(HttpLockEvent $httpLockEvent)
    {

    }


}
