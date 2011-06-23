<?php

namespace Ornicar\MessageBundle\FormModel;

class NewThreadMessage extends AbstractMessage
{
    /**
     * The user who receives the message
     *
     * @var UserInterface
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
     * @param  string
     * @return null
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return UserInterface
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param  UserInterface
     * @return null
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

}
