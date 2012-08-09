<?php

namespace Ornicar\MessageBundle\FormModel;

class NewThreadMessage extends AbstractMessage
{
    /**
     * The user who receives the message
     *
     * @var ParticipantInterface
     */
    protected $recipients = array();

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
     * @param  string
     * @return null
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return array | Doctrine\Common\Collections\ArrayCollection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param  ParticipantInterface
     * @return null
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

}
