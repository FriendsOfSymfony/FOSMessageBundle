<?php

namespace Ornicar\MessageBundle\Sender;

use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;

interface SenderInterface
{
    /**
     * Sends a message, replying to an existing message
     *
     * @param MessageInterface $message the message to send
     * @param ThreadInterface $inReplyToThread the message we answer to
     *
     * @return MessageInterface the message sent
     */
    function sendReply(MessageInterface $message, ThreadInterface $inReplyToThread);

    /**
     * Sends a message, creating a new thread
     *
     * @param MessageInterface $message the message to send
     * @param string $subject the subject of the thread we create
     * @param ParticipantInterface $recipient the user we send the message to
     *
     * @return MessageInterface the message sent
     */
    function sendNewThread(MessageInterface $message, $subject, ParticipantInterface $recipient);
}
