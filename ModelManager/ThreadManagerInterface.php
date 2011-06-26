<?php

namespace Ornicar\MessageBundle\ModelManager;

use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;

/**
 * Interface to be implemented by comment thread managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comment threads should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ThreadManagerInterface extends ReadableManagerInterface
{
    /**
     * Finds a thread by its ID
     *
     * @return ThreadInterface or null
     */
    function findThreadById($id);

    /**
     * Finds not deleted threads for a user,
     * containing at least one message not written by this user,
     * ordered by last message not written by this user in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $user
     * @return Builder a query builder suitable for pagination
     */
    function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $user);

    /**
     * Finds not deleted threads for a user,
     * containing at least one message not written by this user,
     * ordered by last message not written by this user in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $user
     * @return array of ThreadInterface
     */
    function findParticipantInboxThreads(ParticipantInterface $user);

    /**
     * Finds threads from a user,
     * containing at least one message written by this user,
     * ordered by last message written by this user in reverse order.
     * In one word: an sentbox.
     *
     * @param ParticipantInterface $user
     * @return Builder a query builder suitable for pagination
     */
    function getParticipantSentThreadsQueryBuilder(ParticipantInterface $user);

    /**
     * Finds threads from a user,
     * containing at least one message written by this user,
     * ordered by last message written by this user in reverse order.
     * In one word: an sentbox.
     *
     * @param ParticipantInterface $user
     * @return array of ThreadInterface
     */
    function findParticipantSentThreads(ParticipantInterface $user);

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
