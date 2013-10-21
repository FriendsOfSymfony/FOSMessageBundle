<?php

namespace FOS\MessageBundle\EntityManager;

use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Interface used by action managers
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
interface ActionsManagerInterface
{
    /**
     * Saves a new thread to the persistent storage
     *
     * @param ThreadInterface $thread The new thread we save
     */
    public function addThread(ThreadInterface $thread);

    /**
     * Saves a new reply to the persistent storage
     *
     * @param ThreadInterface $thread The thread where we added the new reply message
     */
    public function addReply(ThreadInterface $thread);
}
