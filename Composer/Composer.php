<?php

namespace Ornicar\MessageBundle\Composer;

use Ornicar\MessageBundle\Model\MessageManagerInterface;
use Ornicar\MessageBundle\Sender\MessageSenderInterface;

/**
 * Factory for message builders
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Composer
{
    /**
     * Message manager
     *
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * The message sender
     *
     * @var MessageSenderInterface
     */
    protected $messageSender;

    public function __construct(MessageManagerInterface $messageManager, MessageSenderInterface $messageSender)
    {
        $this->messageManager = $messageManager;
        $this->messageSender = $messageSender;
    }

    /**
     * Starts composing a message, bound to a thread
     *
     * @return MessageBuilder
     */
    public function compose(ThreadInterface $thread = null)
    {
        $message = $this->messageManager->createMessage();

        $builder = new MessageBuilder($this->messageSender);
        $builder->setMessage($message);

        return $builder;
    }
}
