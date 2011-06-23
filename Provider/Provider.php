<?php

namespace Ornicar\MessageBundle\Provider;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ornicar\MessageBundle\Model\ThreadManagerInterface;
use Ornicar\MessageBundle\Authorizer\AuthorizerInterface;

class Provider
{
    /**
     * The authorizer manager
     *
     * @var authorizerInterface
     */
    protected $authorizer;

    /**
     * The thread manager
     *
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    public function __construct(ThreadManagerInterface $threadManager, AuthorizerInterface $authorizer)
    {
        $this->authorizer = $authorizer;
        $this->threadManager = $threadManager;
    }

    /**
     * Gets a thread by its ID
     * Performs authorization checks
     *
     * @return ThreadInterface
     */
    public function getThread($threadId)
    {
        $thread = $this->threadManager->findThreadById($threadId);
        if (!$thread) {
            throw new NotFoundHttpException('There is no such thread');
        }
        if (!$this->authorizer->canSeeThread($thread)) {
            throw new AccessDeniedException('You are not allowed to see this thread');
        }

        return $thread;
    }
}
