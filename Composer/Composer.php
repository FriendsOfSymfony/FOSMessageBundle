<?php

namespace Ornicar\MessageBundle\Composer;

use Ornicar\MessageBundle\ModelManager\MessageManagerInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;

/**
 * Factory for message builders
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Composer implements ComposerInterface
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
     * @var SenderInterface
     */
    protected $messageSender;

    public function __construct(MessageManagerInterface $messageManager, SenderInterface $messageSender)
    {
        $this->messageManager = $messageManager;
        $this->messageSender = $messageSender;
    }

    /**
     * Starts composing a message
     *
     * @return MessageBuilder
     */
    public function compose()
    {
        $message = $this->messageManager->createMessage();

        $builder = new MessageBuilder($this->messageSender);
        $builder->setMessage($message);

        return $builder;
    }
}
