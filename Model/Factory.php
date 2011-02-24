<?php

namespace Bundle\Ornicar\MessageBundle\Model;

use Bundle\Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class Factory
{
    protected $userManager;
    protected $messageRepository;

    public function __construct(UserManagerInterface $userManager, MessageRepositoryInterface $messageRepository)
    {
        $this->userManager= $userManager;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Creates and return a new composition
     *
     * @return Composition
     **/
    public function createComposition()
    {
        return new Composition($this->userManager, $this->messageRepository);
    }

    /**
     * Creates and return a new answer
     *
     * @return Answer
     **/
    public function createAnswer(Message $message)
    {
        $answer = new Answer($this->userManager, $this->messageRepository);
        $answer->setOriginalMessage($message);

        return $answer;
    }
}
