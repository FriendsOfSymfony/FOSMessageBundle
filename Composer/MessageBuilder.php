<?php

namespace Ornicar\MessageBundle\Composer;

use Ornicar\MessageBundle\Model\MessageInterface;
use FOS\UserBundle\Model\UserInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;

/**
 * Fluent interface class to compose messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageBuilder
{
    /**
     * Message manager
     *
     * @var SenderInterface
     */
    protected $messageSender;

    /**
     * The message we are building
     *
     * @var MessageInterface
     */
    protected $message;

    /**
     * The message we are replying to, if any
     *
     * @var MessageInterface
     */
    protected $inReplyToMessage;

    /**
     * The thread subject if we are not replying to a message
     *
     * @var string
     */
    protected $subject;

    public function __construct(SenderInterface $messageSender)
    {
        $this->messageSender = $messageSender;
    }

    /**
     * Sends the created message.
     * See SenderInterface
     *
     * @return MessageInterface the message sent
     */
    public function send()
    {
        // do we send a reply or a new thread?
        if ($this->inReplyToMessage) {
            $this->messageSender->sendReply($this->message, $this->inReplyToMessage);
        } else {
            $this->messageSender->sendNewThread($this->message, $this->subject);
        }

        return $this->message;
    }

    /**
     * Gets the message we are building
     *
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message we are building
     *
     * @param  MessageInterface
     * @return null
     */
    public function setMessage(MessageInterface $message)
    {
        $this->message = $message;
    }

    /**
     * Sets the message we are replying to, if any
     *
     * @param  MessageInterface
     * @return MessageBuilder (fluent interface)
     */
    public function inReplyTo(MessageInterface $inReplyToMessage)
    {
        $this->inReplyToMessage = $inReplyToMessage;

        return $this;
    }

    /**
     * The thread subject if we are not replying to a message
     *
     * @param  string
     * @return MessageBuilder (fluent interface)
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param  string
     * @return MessageBuilder (fluent interface)
     */
    public function setBody($body)
    {
        $this->message->setBody($body);

        return $this;
    }

    /**
     * @param  UserInterface
     * @return MessageBuilder (fluent interface)
     */
    public function setSender(UserInterface $sender)
    {
        $this->message->setSender($sender);

        return $this;
    }

    /**
     * @param  UserInterface
     * @return MessageBuilder (fluent interface)
     */
    public function setRecipient(UserInterface $recipient)
    {
        $this->message->setRecipient($recipient);

        return $this;
    }
}
