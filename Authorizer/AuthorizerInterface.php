<?php

namespace Ornicar\MessageBundle\Authorizer;

use Ornicar\MessageBundle\Model\ThreadInterface;

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
