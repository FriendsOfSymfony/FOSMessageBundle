<?php

namespace FOS\MessageBundle\FormModel;

use FOS\MessageBundle\Model\ParticipantInterface;

class NewThreadMessage extends AbstractMessage
{
    /**
     * The user who receives the message.
     *
     * @var ParticipantInterface
     */
    protected $recipient;

    /**
     * The thread subject.
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
     * @param string
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
     * @param ParticipantInterface
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }
}
