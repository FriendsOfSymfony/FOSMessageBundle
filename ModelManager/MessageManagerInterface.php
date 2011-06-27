<?php

namespace Ornicar\MessageBundle\ModelManager;

use Ornicar\MessageBundle\Model\MessageInterface;

/**
 * Interface to be implemented by message managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to messages should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MessageManagerInterface extends ReadableManagerInterface
{
    /**
     * Creates an empty message instance
     *
     * @return MessageInterface
     */
    function createMessage();

    /**
     * Saves a message
     *
     * @param MessageInterface $message
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function saveMessage(MessageInterface $message, $andFlush = true);

    /**
     * Returns the message's fully qualified class MessageManagerInterface.
     *
     * @return string
     */
    function getClass();
}
