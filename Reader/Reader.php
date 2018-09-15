<?php

namespace FOS\MessageBundle\Reader;

use FOS\MessageBundle\Event\FOSMessageEvents;
use FOS\MessageBundle\Event\ReadableEvent;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\ModelManager\ReadableManagerInterface;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Marks messages and threads as read or unread.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Reader implements ReaderInterface
{
    /**
     * The participantProvider instance.
     *
     * @var ParticipantProviderInterface
     */
    protected $participantProvider;

    /**
     * The readable manager.
     *
     * @var ReadableManagerInterface
     */
    protected $readableManager;

    /**
     * The event dispatcher.
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
     * {@inheritdoc}
     */
    public function markAsRead(ReadableInterface $readable)
    {
        $participant = $this->getAuthenticatedParticipant();
        if ($readable->isReadByParticipant($participant)) {
            return;
        }
        $this->readableManager->markAsReadByParticipant($readable, $participant);

        $this->dispatcher->dispatch(FOSMessageEvents::POST_READ, new ReadableEvent($readable));
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnread(ReadableInterface $readable)
    {
        $participant = $this->getAuthenticatedParticipant();
        if (!$readable->isReadByParticipant($participant)) {
            return;
        }
        $this->readableManager->markAsUnreadByParticipant($readable, $participant);

        $this->dispatcher->dispatch(FOSMessageEvents::POST_UNREAD, new ReadableEvent($readable));
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
