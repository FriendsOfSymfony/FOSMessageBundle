<?php

namespace Ornicar\MessageBundle\Deleter;

use Ornicar\MessageBundle\Authorizer\AuthorizerInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Marks threads as deleted
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Deleter implements ReaderInterface
{
    /**
     * The authorizer instance
     *
     * @var AuthorizerInterface
     */
    protected $authorizer;

    public function __construct(AuthorizerInterface $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Marks the thread as deleted by the current authenticated user
     *
     * @param ThreadInterface $thread
     */
    public function markAsDeleted(ThreadInterface $thread)
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), true);
    }

    /**
     * Marks the thread as undeleted by the current authenticated user
     *
     * @param ThreadInterface $thread
     */
    public function markAsUndeleted(ThreadInterface $thread)
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), false);
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
