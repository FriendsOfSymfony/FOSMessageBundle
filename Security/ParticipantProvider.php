<?php

namespace FOS\MessageBundle\Security;

use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Provides the authenticated participant
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ParticipantProvider implements ParticipantProviderInterface
{
    /** @var  TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    public function getAuthenticatedParticipant()
    {
        $participant = $this->tokenStorage->getToken()->getUser();

        if (!$participant instanceof ParticipantInterface) {
            throw new AccessDeniedException('Must be logged in with a ParticipantInterface instance');
        }

        return $participant;
    }
}
