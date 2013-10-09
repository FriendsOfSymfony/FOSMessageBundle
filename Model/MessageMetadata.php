<?php

namespace FOS\MessageBundle\Model;

/**
 * Abstract metadata storage for a message and participants involved
 * in the thread.
 */
abstract class MessageMetadata
{
    /**
     * @var ParticipantInterface
     */
    protected $participant;

    /**
     * @var Boolean
     */
    protected $isRead = false;

    /**
     * @return ParticipantInterface
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param ParticipantInterface $participant
     */
    public function setParticipant(ParticipantInterface $participant)
    {
        $this->participant = $participant;
    }

    /**
     * @return Boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @param Boolean $isRead
     */
    public function setIsRead($isRead)
    {
        $this->isRead = (boolean) $isRead;
    }
}
