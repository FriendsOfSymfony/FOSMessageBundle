<?php

namespace Ornicar\MessageBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a ParticipantInterface and a username
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UsernameToUserTransformer implements DataTransformerInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Transforms a ParticipantInterface instance to a username string
     *
     * @param mixed $value a ParticipantInterface instance
     * @return string the username
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof ParticipantInterface) {
            throw new UnexpectedTypeException($value, 'ParticipantInterface');
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username to a ParticipantInterface instance
     *
     * @param string $username
     * @return ParticipantInterface the corresponding user instance
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->userManager->findUserByUsername($value);
    }
}
