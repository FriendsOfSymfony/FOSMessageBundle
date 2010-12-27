<?php

namespace Bundle\Ornicar\MessageBundle;

use Bundle\Ornicar\MessageBundle\Model\Message;
use Bundle\Ornicar\MessageBundle\Model\MessageRepositoryInterface;

class Messenger
{
    protected $objectManager;
    protected $messageRepository;

    public function __construct($objectManager, MessageRepositoryInterface $messageRepository)
    {
        $this->objectManager = $objectManager;
        $this->messageRepository = $messageRepository;
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

    public function markAsRead(Message $message)
    {
        if($message->getIsRead()) {
            throw new LogicException('The message is already read');
        }
        $message->setIsRead(true);
    }
}
