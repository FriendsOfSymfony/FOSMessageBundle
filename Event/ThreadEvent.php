<?php

namespace FOS\MessageBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use FOS\MessageBundle\Model\ThreadInterface;

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
