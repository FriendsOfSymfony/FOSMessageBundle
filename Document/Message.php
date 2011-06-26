<?php

namespace Ornicar\MessageBundle\Document;

use Ornicar\MessageBundle\Model\Message as AbstractMessage;
use FOS\UserBundle\Model\UserInterface;

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
     * @return bool
     */
    public function isReadByParticipant(UserInterface $participant)
    {
        return $this->isReadByParticipant[$participant->getId()];
    }

    /**
     * Sets whether or not this participant has read this message
     *
     * @param UserInterface $participant
     * @param boolean $isRead
     */
    public function setIsReadByParticipant(UserInterface $participant, $isRead)
    {
        $this->isReadByParticipant[$participant->getId()] = (boolean) $isRead;
    }
}
