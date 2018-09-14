<?php

namespace FOS\MessageBundle\Entity;

use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\MessageMetadata as BaseMessageMetadata;

abstract class MessageMetadata extends BaseMessageMetadata
{
    protected $id;
    protected $message;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage(MessageInterface $message)
    {
        $this->message = $message;
    }
}
