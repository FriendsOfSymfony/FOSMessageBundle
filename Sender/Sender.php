<?php

namespace Ornicar\MessageBundle\Sender;

use Ornicar\MessageBundle\ModelManager\MessageManagerInterface;
use Ornicar\MessageBundle\ModelManager\ThreadManagerInterface;
use Ornicar\MessageBundle\Model\MessageInterface;

/**
 * Sends messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Sender implements SenderInterface
{
    /**
     * The message manager
     *
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * The thread manager
     *
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    public function __construct(MessageManagerInterface $messageManager, ThreadManagerInterface $threadManager)
    {
        $this->messageManager = $messageManager;
        $this->threadManager = $threadManager;
    }

    /**
     * Sends the message by persisting it to the message manager
     *
     * @param MessageInterface $message
     */
    public function send(MessageInterface $message)
    {
        $this->threadManager->saveThread($message->getThread(), false);
        $this->messageManager->saveMessage($message);
    }
}
