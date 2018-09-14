<?php

namespace FOS\MessageBundle\Tests\Functional\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        return new User();
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }

    private function fetchUser($username)
    {
        return new User();
    }
}
