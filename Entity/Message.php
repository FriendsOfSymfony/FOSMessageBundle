<?php

namespace Ornicar\MessageBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ornicar\MessageBundle\Model\Message as BaseMessage;

use Ornicar\MessageBundle\Model\MessageMetadata as ModelMessageMetadata;

abstract class Message extends BaseMessage
{
    /**
     * Get the collection of MessageMetadata.
     *
     * @return Collection
     */
    public function getAllMetadata()
    {
        return $this->metadata;
    }

    /**
     * @see Ornicar\MessageBundle\Model\Message::addMetadata()
     */
    public function addMetadata(ModelMessageMetadata $meta)
    {
        $meta->setMessage($this);
        parent::addMetadata($meta);
    }
}
