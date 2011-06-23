<?php

namespace Ornicar\MessageBundle\Model;

use DateTime;
use FOS\UserBundle\Model\UserInterface;

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
     * Tells if all messages this user is the recipient of are read
     *
     * @return bool
     */
    public function isReadByUser(UserInterface $user)
    {
        foreach ($this->getMessages() as $message) {
            if ($user === $message->getRecipient() && !$message->getIsRead()) {
                return false;
            }
        }

        return true;
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
}
