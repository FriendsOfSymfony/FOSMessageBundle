<?php

namespace Ornicar\MessageBundle\Reader;

use Ornicar\MessageBundle\Security\ParticipantProviderInterface;
use Ornicar\MessageBundle\Model\ReadableInterface;
use Ornicar\MessageBundle\ModelManager\ReadableManagerInterface;
use Ornicar\MessageBundle\Event\OrnicarMessageEvents;
use Ornicar\MessageBundle\Event\ReadableEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Marks messages and threads as read or unread
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Reader implements ReaderInterface
{
    /**
     * The participantProvider instance
     *
     * @var ParticipantProviderInterface
     */
    protected $participantProvider;

    /**
     * The readable manager
     *
     * @var ReadableManagerInterface
     */
    protected $readableManager;

    /**
     * The event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(ParticipantProviderInterface $participantProvider, ReadableManagerInterface $readableManager, EventDispatcherInterface $dispatcher)
    {
        $this->participantProvider = $participantProvider;
        $this->readableManager = $readableManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Marks the readable as read by the current authenticated user
     *
     * @param ReadableInterface $readable
     */
    public function markAsRead(ReadableInterface $readable)
    {
        $participant = $this->getAuthenticatedParticipant();
        if ($readable->isReadByParticipant($participant)) {
            return;
        }
        $this->readableManager->markAsReadByParticipant($readable, $participant);

        $this->dispatcher->dispatch(OrnicarMessageEvents::POST_READ, new ReadableEvent($readable));
    }

    /**
     * Marks the readable as unread by the current authenticated user
     *
     * @param ReadableInterface $readable
     */
    public function markAsUnread(ReadableInterface $readable)
    {
        $participant = $this->getAuthenticatedParticipant();
        if (!$readable->isReadByParticipant($participant)) {
            return;
        }
        $this->readableManager->markAsReadByParticipant($readable, $participant);

        $this->dispatcher->dispatch(OrnicarMessageEvents::POST_UNREAD, new ReadableEvent($readable));
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
