<?php

namespace FOS\MessageBundle\Form\Model;

use FOS\MessageBundle\Model\ParticipantInterface;

class NewThreadMessage extends AbstractMessage
{
    /**
     * The user who receives the message
     *
     * @var ParticipantInterface
     */
    protected $recipient;

    /**
     * The thread subject
     *
     * @var string
     */
    protected $subject;

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return ParticipantInterface
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param ParticipantInterface $recipient
     */
    public function setRecipient(ParticipantInterface $recipient)
    {
        $this->recipient = $recipient;
    }

}
