<?php

namespace FOS\MessageBundle\Event;

use FOS\MessageBundle\Model\ThreadInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ThreadEvent extends Event
{
    /**
     * @var ThreadInterface
     */
    private $thread;

    public function __construct(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }
}
