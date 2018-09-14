<?php

namespace FOS\MessageBundle\Deleter;

use FOS\MessageBundle\Event\FOSMessageEvents;
use FOS\MessageBundle\Event\ThreadEvent;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Security\AuthorizerInterface;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Marks threads as deleted.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Deleter implements DeleterInterface
{
    /**
     * The authorizer instance.
     *
     * @var AuthorizerInterface
     */
    protected $authorizer;

    /**
     * The participant provider instance.
     *
     * @var ParticipantProviderInterface
     */
    protected $participantProvider;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(AuthorizerInterface $authorizer, ParticipantProviderInterface $participantProvider, EventDispatcherInterface $dispatcher)
    {
        $this->authorizer = $authorizer;
        $this->participantProvider = $participantProvider;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsDeleted(ThreadInterface $thread)
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), true);

        $this->dispatcher->dispatch(FOSMessageEvents::POST_DELETE, new ThreadEvent($thread));
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUndeleted(ThreadInterface $thread)
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), false);

        $this->dispatcher->dispatch(FOSMessageEvents::POST_UNDELETE, new ThreadEvent($thread));
    }

    /**
     * Gets the current authenticated user.
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
