<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\MessageMetadata as BaseMessageMetadata;
use Ornicar\MessageBundle\Model\MessageInterface;

abstract class MessageMetadata extends BaseMessageMetadata
{
    protected $id;

    protected $message;

    /**
     * Gets the metadata id
     *
     * @return integer
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

    /**
     * @param  MessageInterface
     * @return null
     */
    public function setMessage(MessageInterface $message)
    {
        $this->message = $message;
    }
}
