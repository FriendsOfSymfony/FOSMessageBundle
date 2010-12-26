<?php

namespace Bundle\Ornicar\MessageBundle;

use Bundle\Ornicar\MessageBundle\Model\Message;

class Messenger
{
    protected $objectManager;

    public function __construct($objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function send(Message $message)
    {
        if(!$message->getFrom()) {
            throw new LogicException('The message has no from');
        }
        if(!$message->getTo()) {
            throw new LogicException('The message has no to');
        }
        $this->objectManager->persist($message);
    }
}
