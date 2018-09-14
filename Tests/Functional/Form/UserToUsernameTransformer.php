<?php

namespace FOS\MessageBundle\Tests\Functional\Form;

use FOS\MessageBundle\Tests\Functional\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class UserToUsernameTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof User) {
            throw new \RuntimeException();
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a UserInterface instance.
     *
     * @param string $value Username
     *
     * @return UserInterface the corresponding UserInterface instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value)
    {
        return new User();
    }
}
