<?php

namespace Ornicar\MessageBundle\Model;

use Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class Factory
{
    
    protected $messageClass;
    protected $messageRepository;
    protected $userManager;
    
    public function __construct($messageClass, UserManagerInterface $userManager, MessageRepositoryInterface $messageRepository)
    {
        $this->messageClass = $messageClass;
        $this->userManager = $userManager;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Creates and returns a new message
     *
     * @return Message
     */
    public function createComposition()
    {
        $class = $this->getMessageClass();
        return new $class();
    }

    /**
     * Creates and return a new answer
     *
     * @return Message
     */
    public function createAnswer(Message $message)
    {
        $class = $this->getMessageClass();
        
        $answer = new $class();
        $answer->setTo($message->getFrom());
        $answer->setSubject(preg_replace('/^(Re:\s)*/', 'Re: ', $message->getSubject()));
        
        return $answer;
    }
    
    /**
     * Get the message class
     * 
     * @return string
     */
    protected function getMessageClass()
    {
        return $this->messageClass;
    }
}
