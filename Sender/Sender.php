<?php

namespace Ornicar\MessageBundle\Sender;

use Ornicar\MessageBundle\ModelManager\MessageManagerInterface;
use Ornicar\MessageBundle\ModelManager\ThreadManagerInterface;
use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\Event\MessageEvent;
use Ornicar\MessageBundle\Event\OrnicarMessageEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    /**
     * The event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(MessageManagerInterface $messageManager, ThreadManagerInterface $threadManager, EventDispatcherInterface $dispatcher)
    {
        $this->messageManager = $messageManager;
        $this->threadManager = $threadManager;
        $this->dispatcher = $dispatcher;
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

        $this->dispatcher->dispatch(OrnicarMessageEvents::POST_SEND, new MessageEvent($message));
    }
}
