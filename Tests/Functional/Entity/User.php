<?php

namespace FOS\MessageBundle\Tests\Functional\Entity;

use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements ParticipantInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    public function getUserIdentifier(): string
    {
        return 'guilhem';
    }

    public function getPassword(): ?string
    {
        return 'pass';
    }

    public function getSalt()
    {
    }

    public function getRoles(): array
    {
        return array();
    }

    public function eraseCredentials()
    {
    }

    public function getId()
    {
        return 1;
    }
}
