<?php

namespace Ornicar\MessageBundle\Sender;

use Ornicar\MessageBundle\Model\MessageInterface;

interface SenderInterface
{
    /**
     * Sends a message, replying to an existing message
     *
     * @param MessageInterface $message the message to send
     * @param MessageInterface $inReplyToMessage the message we answer to
     *
     * @return MessageInterface the message sent
     */
    function sendReply(MessageInterface $message, MessageInterface $inReplyToMessage);

    /**
     * Sends a message, creating a new thread
     *
     * @param MessageInterface $message the message to send
     * @param string $subject the subject of the thread we create
     *
     * @return MessageInterface the message sent
     */
    function sendNewThread(MessageInterface $message, $subject);
}
