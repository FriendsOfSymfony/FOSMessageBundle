<?php

namespace Ornicar\MessageBundle\Model;

use DateTime;
use Ornicar\MessageBundle\Model\ParticipantInterface;

/**
 * Abstract thread model
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Thread implements ThreadInterface
{
    /**
     * Unique id of the thread
     *
     * @var mixed
     */
    protected $id;

    /**
     * Text subject of the thread
     *
     * @var string
     */
    protected $subject;

    /**
     * Tells if the thread is spam or flood
     *
     * @var boolean
     */
    protected $isSpam = false;

    /**
     * Gets the message unique id
     *
     * @return mixed
     **/
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param  string
     * @return null
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return boolean
     */
    public function getIsSpam()
    {
        return $this->isSpam;
    }

    /**
     * @param  boolean
     * @return null
     */
    public function setIsSpam($isSpam)
    {
        $this->isSpam = (boolean) $isSpam;
    }

    /**
     * Tells if all messages of this participant are read
     *
     * @return bool
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        foreach ($this->getMessages() as $message) {
            if (!$message->isReadByParticipant($participant)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets whether or not this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        foreach ($this->getMessages() as $message) {
            $message->setIsReadByParticipant($participant, $isRead);
        }
    }

    /**
     * Gets the first message of the thread
     *
     * @return MessageInterface the first message
     */
    public function getFirstMessage()
    {
        $messages = $this->getMessages();
        if(empty($messages)) {
            return null;
        }

        return reset($messages);
    }

    /**
     * Gets the last message of the thread
     *
     * @return MessageInterface the last message
     */
    public function getLastMessage()
    {
        $messages = $this->getMessages();
        if(empty($messages)) {
            return null;
        }

        return end($messages);
    }

    /**
     * Get the participants this participant is talking with.
     *
     * @return array of ParticipantInterface
     */
    public function getOtherParticipants(ParticipantInterface $participant)
    {
        $otherParticipants = $this->getParticipants();

        unset($otherParticipants[array_search($participant, $otherParticipants)]);

        // we want to reset the array indexes
        return array_values($otherParticipants);
    }
}
