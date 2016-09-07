<?php

namespace FOS\MessageBundle\Deleter;

use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Marks threads as deleted.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface DeleterInterface
{
    /**
     * Marks the thread as deleted by the current authenticated user.
     */
    public function markAsDeleted(ThreadInterface $thread);

    /**
     * Marks the thread as undeleted by the current authenticated user.
     */
    public function markAsUndeleted(ThreadInterface $thread);
}
