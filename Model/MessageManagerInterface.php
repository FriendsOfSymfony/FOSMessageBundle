<?php

namespace Ornicar\MessageBundle\Model;

/**
 * Interface to be implemented by message managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to messages should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MessageManagerInterface
{
    /**
     * Creates an empty message instance, bound to a thread
     *
     * @param ThreadInterface $thread
     * @return UserInterface
     */
    function createUser(ThreadInterface $thread);

    /**
     * Saves a message
     *
     * @param MessageInterface $message
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateMessage(MessageInterface $message, $andFlush = true);

    /**
     * Returns the message's fully qualified class name.
     *
     * @return string
     */
    function getClass();
}
