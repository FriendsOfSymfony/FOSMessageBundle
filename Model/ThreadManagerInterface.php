<?php

namespace Ornicar\MessageBundle\Model;

use FOS\UserBundle\Model\UserInterface;

/**
 * Interface to be implemented by comment thread managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comment threads should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ThreadManagerInterface
{
    /**
     * Finds a thread by its ID
     *
     * @return ThreadInterface or null
     */
    function findThreadById($id);

    /**
     * Find threads for a user
     * Order them by last message not written by this user
     *
     * @param UserInterface $user
     * @return Builder a query builder suitable for pagination
     */
    function getUserInboxThreadsQueryBuilder(UserInterface $user);

    /**
     * Find threads for a user
     * Order them by last message not written by this user
     *
     * @param UserInterface $user
     * @return array of ThreadInterface
     */
    function findUserInboxThreads(UserInterface $user);

    /**
     * Creates an empty comment thread instance
     *
     * @return ThreadInterface
     */
    function createThread();

    /**
     * Deletes a thread
     *
     * @param ThreadInterface $thread the thread to delete
     */
    function deleteThread(ThreadInterface $thread);

    /**
     * Saves a thread
     *
     * @param ThreadInterface $thread
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateThread(ThreadInterface $thread, $andFlush = true);
}
