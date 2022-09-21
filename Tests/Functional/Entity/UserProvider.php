<?php

namespace FOS\MessageBundle\Tests\Functional\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier($username): UserInterface
    {
        return new User();
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }

    private function fetchUser($username)
    {
        return new User();
    }

    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }
}
