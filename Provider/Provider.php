<?php

namespace Ornicar\MessageBundle\Provider;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ornicar\MessageBundle\ModelManager\ThreadManagerInterface;
use Ornicar\MessageBundle\Authorizer\AuthorizerInterface;
use Ornicar\MessageBundle\Reader\ReaderInterface;

/**
 * Provides threads for the current authenticated user
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Provider implements ProviderInterface
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

    /**
     * The reader used to mark threads as read
     *
     * @var ReaderInterface
     */
    protected $threadReader;

    public function __construct(ThreadManagerInterface $threadManager, AuthorizerInterface $authorizer, ReaderInterface $threadReader)
    {
        $this->authorizer = $authorizer;
        $this->threadManager = $threadManager;
        $this->threadReader = $threadReader;
    }

    /**
     * Gets the thread in the inbox of the current user
     *
     * @return array of ThreadInterface
     */
    public function getInboxThreads()
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->findParticipantInboxThreads($participant);
    }

    /**
     * Gets the thread in the sentbox of the current user
     *
     * @return array of ThreadInterface
     */
    public function getSentThreads()
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->findParticipantSentThreads($participant);
    }

    /**
     * Gets a thread by its ID
     * Performs authorization checks
     * Marks the thread as read
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
        // Load the thread messages before marking them as read
        // because we want to see the unread messages
        $thread->getMessages();
        $this->threadReader->markAsRead($thread);

        return $thread;
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->authorizer->getAuthenticatedParticipant();
    }
}
