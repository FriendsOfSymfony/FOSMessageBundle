<?php

namespace FOS\MessageBundle\Tests\Functional\Form;

use FOS\MessageBundle\Tests\Functional\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class UserToUsernameTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof User) {
            throw new \RuntimeException();
        }

        return $value->getUserIdentifier();
    }

    /**
     * Transforms a username string into a UserInterface instance.
     *
     * @param string $value Username
     *
     * @throws UnexpectedTypeException if the given value is not a string
     *
     * @return UserInterface the corresponding UserInterface instance
     */
    public function reverseTransform($value)
    {
        return new User();
    }
}
