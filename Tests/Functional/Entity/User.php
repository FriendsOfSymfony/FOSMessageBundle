<?php

namespace FOS\MessageBundle\Tests\Functional\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;

class User implements ParticipantInterface
{
    public function getUsername()
    {
        return 'Guilhem';
    }

   public function getId()
   {
       return 1;
   }
}
