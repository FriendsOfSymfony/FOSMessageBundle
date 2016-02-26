<?php

namespace FOS\MessageBundle\Event;

use FOS\MessageBundle\Model\ReadableInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ReadableEvent extends Event
{
    /**
     * The readable
     * @var ReadableInterface
     */
    private $readable;

    public function __construct(ReadableInterface $readable)
    {
        $this->readable = $readable;
    }

    /**
     * Returns the readable
     *
     * @return ReadableInterface
     */
    public function getReadable()
    {
        return $this->readable;
    }
}
