<?php

namespace Ornicar\MessageBundle\Sender;

use Ornicar\MessageBundle\Model\MessageManagerInterface;
use Ornicar\MessageBundle\Model\ThreadManagerInterface;
use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;
use FOS\UserBundle\Model\UserInterface;

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
     * Sends a message, replying to an existing message
     *
     * @param MessageInterface $message the message to send
     * @param ThreadInterface $inReplyToThread the thread we answer to
     *
     * @return MessageInterface the message sent
     */
    public function sendReply(MessageInterface $message, ThreadInterface $inReplyToThread)
    {
        return $this->doSend($message, $inReplyToThread);
    }

    /**
     * Sends a message, creating a new thread
     *
     * @param MessageInterface $message the message to send
     * @param string $subject the subject of the thread we create
     * @param UserInterface $recipient the user we send the message to
     *
     * @return MessageInterface the message sent
     */
    public function sendNewThread(MessageInterface $message, $subject, UserInterface $recipient)
    {
        $thread = $this->threadManager->createThread();
        $thread->setSubject($subject);
        $thread->addParticipant($recipient);
        $this->threadManager->updateThread($thread, false);

        return $this->doSend($message, $thread);
    }

    /**
     * Binds the message and the thread together,
     * and saves them
     *
     * @param MessageInterface $message
     * @param ThreadInterface $thread
     * @return MessageInterface
     */
    protected function doSend(MessageInterface $message, ThreadInterface $thread)
    {
        $message->setThread($thread);
        $thread->addMessage($message);
        $this->messageManager->updateMessage($message);

        return $message;
    }
}
