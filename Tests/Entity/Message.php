<?php

namespace FOS\MessageBundle\Tests\Entity;

use FOS\MessageBundle\Entity\Message as BaseMessage;

class Message extends BaseMessage
{
    public function setCreatedAt(\DateTime $dateTime)
    {
        $this->createdAt = $dateTime;
    }
}
