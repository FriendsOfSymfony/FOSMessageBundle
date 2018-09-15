<?php

namespace FOS\MessageBundle\Security;

use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Manages permissions to manipulate threads and messages.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Authorizer implements AuthorizerInterface
{
    /**
     * @var ParticipantProviderInterface
     */
    protected $participantProvider;

    public function __construct(ParticipantProviderInterface $participantProvider)
    {
        $this->participantProvider = $participantProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function canSeeThread(ThreadInterface $thread)
    {
        return $this->getAuthenticatedParticipant() && $thread->isParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * {@inheritdoc}
     */
    public function canDeleteThread(ThreadInterface $thread)
    {
        return $this->canSeeThread($thread);
    }

    /**
     * {@inheritdoc}
     */
    public function canMessageParticipant(ParticipantInterface $participant)
    {
        return true;
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
