<?php

namespace FOS\MessageBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use FOS\MessageBundle\Model\MessageInterface;

class MessageEvent extends ThreadEvent
{
    /**
     * The message
     * @var MessageInterface
     */
    private $message;

    public function __construct(MessageInterface $message)
    {
        parent::__construct($message->getThread());

        $this->message = $message;
    }

    /**
     * Returns the message
     *
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }
}
