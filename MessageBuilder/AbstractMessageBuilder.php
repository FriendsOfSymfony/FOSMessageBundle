<?php

namespace Ornicar\MessageBundle\MessageBuilder;

use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;

/**
 * Fluent interface message builder
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageBuilder
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
     * The message the message goes in
     *
     * @var ThreadInterface
     */
    protected $thread;

    public function __construct(MessageInterface $message, ThreadInterface $thread)
    {
        $this->message = $message;
        $this->thread = $thread;

        $this->message->setThread($thread);
        $thread->addMessage($message);
    }

    /**
     * Gets the created message.
     *
     * @return MessageInterface the message created
     */
    public function getMessage()
    {
        return $this->message;
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
     * @param  ParticipantInterface
     * @return MessageBuilder (fluent interface)
     */
    public function setSender(ParticipantInterface $sender)
    {
        $this->message->setSender($sender);

        return $this;
    }
}
