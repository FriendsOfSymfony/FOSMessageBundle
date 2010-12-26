<?php

namespace Bundle\Ornicar\MessageBundle\Model;

use Bundle\Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use Bundle\FOS\UserBundle\Model\UserRepositoryInterface;

class Composition
{
    protected $userRepository;
    protected $messageRepository;

    /**
     * Username of the user who will receive the message
     *
     * @var string
     * @validation:NotBlank(message="Missing to")
     */
    public $to = null;

    /**
     * Text body of the message
     *
     * @var string
     * @validation:NotBlank(message="Please write a message")
     * @validation:MinLength(limit=4, message="Just a little too short.")
     */
    public $body = null;

    /**
     * Text subject of the message
     *
     * @var string
     * @validation:NotBlank(message="Please write a subject")
     * @validation:MinLength(limit=2, message="Just a little too short.")
     */
    public $subject = null;

    public function __construct(UserRepositoryInterface $userRepository, MessageRepositoryInterface $messageRepository)
    {
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
    }

    /**
     * @validation:NotNull(message="This user does not exist")
     */
    public function getTo()
    {
        return $this->userRepository->findOneByUsername($this->to);
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
