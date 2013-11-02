<?php

namespace FOS\MessageBundle\Entity;

use FOS\MessageBundle\Model\Thread as BaseThread;
use FOS\MessageBundle\Model\ParticipantInterface;

use FOS\MessageBundle\Model\ThreadMetadata as ModelThreadMetadata;

abstract class Thread extends BaseThread
{
    /**
     * All text contained in the thread messages
     * Used for the full text search
     *
     * @var string
     */
    protected $keywords = '';

    /**
     * @param ModelThreadMetadata $meta
     */
    public function addMetadata(ModelThreadMetadata $meta)
    {
        parent::addMetadata($meta);

        $meta->setThread($this);
    }
}
