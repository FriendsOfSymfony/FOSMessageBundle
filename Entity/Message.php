<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\Message as BaseMessage;
use Ornicar\MessageBundle\Model\ParticipantInterface;

abstract class Message extends BaseMessage
{
    /**
     * Tells if this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @return boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * Sets whether or not this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * Ensures that each participant has an isRead flag
     *
     * @param array $participants list of ParticipantInterface
     */
    public function ensureIsReadByParticipant(array $participants)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * Gets the created at timestamp
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->getCreatedAt()->getTimestamp();
    }
}
