<?php

namespace Bundle\Ornicar\MessageBundle\Model;

use Bundle\FOS\UserBundle\Model\UserRepositoryInterface;

class Composition
{
    protected $userRepository;

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

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @validation:NotNull(message="This user does not exist")
     */
    public function getTo()
    {
        return $this->userRepository->findOneByUsername($this->to);
    }
}
