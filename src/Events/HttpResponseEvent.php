<?php
namespace GuzzleDxy\Events;

use GuzzleDxy\Result;
use Symfony\Contracts\EventDispatcher\Event;

class HttpResponseEvent extends Event
{

    private $result;

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

}