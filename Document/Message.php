<?php

namespace Ornicar\MessageBundle\Document;

use Ornicar\MessageBundle\Model\Message as AbstractMessage;
use Ornicar\MessageBundle\Model\ParticipantInterface;

abstract class Message extends AbstractMessage
{
    /**
     * Tells, for each participant, if the message is read
     *
     * @var array of boolean indexed by user id
     */
    protected $isReadByParticipant = array();

    /**
     * Tells if this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @return boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        return $this->isReadByParticipant[$participant->getId()];
    }

    /**
     * Sets whether or not this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        $this->isReadByParticipant[$participant->getId()] = (boolean) $isRead;
    }

    /**
     * Ensures that each participant has an isRead flag
     *
     * @param array $participants list of ParticipantInterface
     */
    public function ensureIsReadByParticipant(array $participants)
    {
        foreach ($participants as $participant) {
            if (!isset($this->isReadByParticipant[$participant->getId()])) {
                $this->isReadByParticipant[$participant->getId()] = false;
            }
        }
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
