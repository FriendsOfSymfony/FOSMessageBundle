<?php

namespace FOS\MessageBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use FOS\MessageBundle\Model\ReadableInterface;

class ReadableEvent extends Event
{
    /**
     * @var ReadableInterface
     */
    private $readable;

    public function __construct(ReadableInterface $readable)
    {
        $this->readable = $readable;
    }

    /**
     * @return ReadableInterface
     */
    public function getReadable()
    {
        return $this->readable;
    }
}
