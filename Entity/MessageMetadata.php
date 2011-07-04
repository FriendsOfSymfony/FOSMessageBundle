<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Model\MessageInterface;


abstract class MessageMetadata
{
    protected $id;

    protected $participant;
    protected $message;

    protected $isRead = 0;

    /**
     * Gets the metadata id
     *
     * @return integer
     **/
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

    /**
     * @return ParticipantInterface
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param  ParticipantInterface
     * @return null
     */
    public function setParticipant(ParticipantInterface $participant)
    {
        $this->participant = $participant;
    }

    /**
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @param  boolean
     * @return null
     */
    public function setIsRead($isRead)
    {
        $this->isRead = (boolean)$isRead;
    }
}
