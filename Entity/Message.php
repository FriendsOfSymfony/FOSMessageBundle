<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\Message as BaseMessage;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Message extends BaseMessage
{
    /**
     * Message metadata
     *
     * @var Collection of MessageMetadata
     */
    protected $metadata;

    /**
     * Initializes the collections
     */
    public function __construct()
    {
        parent::__construct();
        $this->metadata = new ArrayCollection();
    }

    /**
     * Tells if this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @return boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsRead();
        }

        return false;
    }

    /**
     * Sets whether or not this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        $meta = $this->getMetadataForParticipant($participant);
        if (!$meta) {
            throw new \Exception(sprintf('No metadata setted for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsRead($isRead);
    }

    public function getAllMetadata()
    {
        return $this->metadata;
    }

    public function getMetadataForParticipant($participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }

    public function addMetadata(MessageMetadata $meta)
    {
        $meta->setMessage($this);
        $this->metadata->add($meta);
    }
}
