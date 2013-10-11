<?php

namespace FOS\MessageBundle\Entity;

use FOS\MessageBundle\Model\Message as BaseMessage;
use FOS\MessageBundle\Model\MessageMetadata as ModelMessageMetadata;

abstract class Message extends BaseMessage
{
    /**
     * @param ModelMessageMetadata $meta
     */
    public function addMetadata(ModelMessageMetadata $meta)
    {
        parent::addMetadata($meta);

        $meta->setMessage($this);
    }
}
