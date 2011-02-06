<?php

namespace Bundle\Ornicar\MessageBundle\Model;

use Bundle\Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use FOS\UserBundle\Model\UserRepositoryInterface;

class Answer extends Composition
{
    public function setOriginalMessage(Message $message)
    {
        $this->to = $message->getFrom();
        $this->subject = preg_replace('/^(Re:\s)*/', 'Re: ', $message->getSubject());
    }

    public function getMessage()
    {
        $message = $this->messageRepository->createNewMessage();
        $message->setTo($this->getTo());
        $message->setSubject($this->subject);
        $message->setBody($this->body);

        return $message;
    }
}
