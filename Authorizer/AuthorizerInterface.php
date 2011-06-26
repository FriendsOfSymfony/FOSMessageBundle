<?php

namespace Ornicar\MessageBundle\Authorizer;

use Ornicar\MessageBundle\Model\ThreadInterface;

/**
 * Provides the authenticated participant,
 * and manages permissions to manipulate threads and messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface AuthorizerInterface
{
    /**
     * Tells if the current user is allowed
     * to see this thread
     *
     * @param ThreadInterface $thread
     * @return boolean
     */
    function canSeeThread(ThreadInterface $thread);
}
