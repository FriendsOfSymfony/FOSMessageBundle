<?php

namespace Ornicar\MessageBundle\Sender;

use Ornicar\MessageBundle\Model\MessageInterface;

/**
 * Sends messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface SenderInterface
{
    /**
     * Sends the message
     *
     * @param MessageInterface $message
     */
    function send(MessageInterface $message);
}
