<?php

namespace FOS\MessageBundle\Tests\Functional\Entity;

use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements ParticipantInterface, UserInterface
{
    public function getUsername()
    {
        return 'guilhem';
    }

    public function getPassword()
    {
        return 'pass';
    }

    public function getSalt()
    {
    }

    public function getRoles()
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
