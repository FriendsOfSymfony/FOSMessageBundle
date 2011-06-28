<?php

namespace Ornicar\MessageBundle\Composer;

use Ornicar\MessageBundle\ModelManager\MessageManagerInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\ModelManager\ThreadManagerInterface;
use Ornicar\MessageBundle\MessageBuilder\NewThreadMessageBuilder;
use Ornicar\MessageBundle\MessageBuilder\ReplyMessageBuilder;

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
     * Thread manager
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
     * Starts composing a message, starting a new thread
     *
     * @return NewThreadMessageBuilder
     */
    public function newThread()
    {
        $thread = $this->threadManager->createThread();
        $message = $this->messageManager->createMessage();

        return new NewThreadMessageBuilder($message, $thread);
    }

    /**
     * Starts composing a message in a reply to a thread
     *
     * @return ReplyMessageBuilder
     */
    public function reply(ThreadInterface $thread)
    {
        $message = $this->messageManager->createMessage();

        return new ReplyMessageBuilder($message, $thread);
    }
}
