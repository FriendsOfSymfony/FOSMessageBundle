<?php

namespace Ornicar\MessageBundle\Provider;

/**
 * Provides threads for the current authenticated user
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Gets the thread in the inbox of the current user
     *
     * @return array of ThreadInterface
     */
    function getInboxThreads();

    /**
     * Gets the thread in the sentbox of the current user
     *
     * @return array of ThreadInterface
     */
     function getSentThreads();

    /**
     * Gets a thread by its ID
     * Performs authorization checks
     * Marks the thread as read
     *
     * @return ThreadInterface
     */
    function getThread($threadId);
}
